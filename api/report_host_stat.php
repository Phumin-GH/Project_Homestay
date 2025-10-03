<?php
// /api/get_host_booking_stats.php
session_start();
header('Content-Type: application/json');


$host_id = $_GET['Host_id'];

require_once __DIR__ . '/../model/config/db_connect.php';

$sql = "
    SELECT 
        b.Booking_status,
        COUNT(b.Booking_id) as status_count
    FROM booking b
    INNER JOIN property p ON b.Property_id = p.Property_id
    WHERE p.Host_id = ?
    GROUP BY b.Booking_status;
";
$stmt = $conn->prepare($sql);
$stmt->execute([$host_id]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stats_data = ['labels' => [], 'data' => []];
foreach ($results as $row) {
    $stats_data['labels'][] = ucfirst($row['Booking_status']); // ทำให้ตัวแรกเป็นพิมพ์ใหญ่
    $stats_data['data'][] = (int)$row['status_count'];
}
echo json_encode($stats_data);
