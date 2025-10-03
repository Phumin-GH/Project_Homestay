<?php
// /api/get_host_revenue.php
session_start();
header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/../model/config/db_connect.php';
// 1. ตรวจสอบสิทธิ์: เช็คว่าโฮสต์ล็อกอินอยู่หรือไม่
$host_email = $_GET['id'];
$sql = 'SELECT Host_id FROM host WHERE Host_email=?';
$stmt = $conn->prepare($sql);
$stmt->execute([$host_email]);
$result = $stmt->fetchColumn();

if (isset($_GET['type'])) {
    $type = $_GET['type'];
    switch ($type) {
        case 'revenue':
            $sql = "SELECT DATE_FORMAT(t.Created_at, '%Y-%m') AS month, 
            SUM(t.Total_amount) AS total_revenue, SUM(t.Platform_fee) AS total_revenue_plat 
            ,SUM(t.Host_payout) AS total_revenue_host FROM transactions t 
            LEFT JOIN booking b ON t.Booking_id = b.Booking_id 
            LEFT JOIN property p ON b.Property_id = p.Property_id 
            WHERE p.Host_id = ? AND b.Payment_status = 'paid' 
            GROUP BY DATE_FORMAT(t.Created_at, '%Y-%m') ORDER BY month ASC;
            ";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$result]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $revenue_data = ['labels' => [], 'total_revenue' => [], 'total_revenue_plat' => [], 'data' => []];
            foreach ($results as $row) {
                $dateObj = DateTime::createFromFormat('!Y-m', $row['month']);
                $revenue_data['labels'][] = $dateObj->format('F Y');
                $revenue_data['total_revenue'][] = (float) $row['total_revenue'];
                $revenue_data['total_revenue_plat'][] = (float) $row['total_revenue_plat'];
                $revenue_data['total_revenue_host'][] = (float) $row['total_revenue_host'];
            }
            echo json_encode($revenue_data);
            break;

        case 'property':
            $sql = "SELECT p.Property_name AS House, COUNT(b.Booking_id) AS booking_count
            FROM booking b
            INNER JOIN property p ON b.Property_id = p.Property_id
            LEFT JOIN host h ON p.Host_id = h.Host_id
            WHERE b.Booking_status = 'successful' AND b.Create_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
            AND h.Host_id =?
            GROUP BY p.Property_id, p.Property_name
            UNION ALL 
            SELECT p.Property_name AS House, COUNT(w.Walkin_id) AS booking_count
            FROM walkin w
            INNER JOIN property p ON w.Property_id = p.Property_id
            LEFT JOIN host h ON p.Host_id = h.Host_id
            WHERE w.Walkin_status = 'successful' AND w.Create_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
            AND h.Host_id =?
            GROUP BY p.Property_id, p.Property_name
            ORDER BY booking_count DESC LIMIT 5;";

            $stmt = $conn->prepare($sql);
            $stmt->execute([$result, $result]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


            $data = ['labels' => [], 'data' => []];
            foreach ($results as $row) {
                $data['labels'][] = $row['House'];
                $data['data'][] = (int) $row['booking_count'];
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;
        case 'booking':
            $sql = "SELECT
    b.Booking_status AS type_status,
    COUNT(DISTINCT b.Booking_id) AS number_of_bookings,
    SUM(CASE WHEN t.transaction_type = 'booking' THEN t.total_amount ELSE 0 END) AS total_income,
    SUM(CASE WHEN t.transaction_type = 'refund' THEN t.total_amount ELSE 0 END) AS total_refunds
    FROM booking b
    INNER JOIN property p ON b.Property_id = p.Property_id
    LEFT JOIN transactions t ON b.Booking_id = t.Booking_id
    WHERE p.Host_id = ?
    GROUP BY b.Booking_status;
            ";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$result]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stats_data = ['labels' => [], 'data' => []];
            foreach ($results as $row) {
                $stats_data['labels'][] = ucfirst($row['type_status']); // ทำให้ตัวแรกเป็นพิมพ์ใหญ่
                $stats_data['number'][] = (int) $row['number_of_bookings'];
                $stats_data['income'][] = (int) $row['total_income'];
                $stats_data['refunds'][] = (int) $row['total_refunds'];
            }
            echo json_encode($stats_data);
            break;
        case 'gateway':
    }
}
