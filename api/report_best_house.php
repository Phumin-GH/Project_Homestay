<?php
// /api/get_daily_new_users.php
header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php'; // <<-- ตรวจสอบ Path ให้ถูกต้อง

$sql = "SELECT 
    p.Property_name AS House,
    COUNT(b.Booking_id) AS booking_count
FROM 
    booking b
INNER JOIN 
    property p ON b.Property_id = p.Property_id
WHERE 
    b.Booking_status = 'successful' AND b.Create_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY 
    p.Property_id, p.Property_name
ORDER BY 
    booking_count DESC
LIMIT 5;
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// เตรียมข้อมูลสำหรับส่งกลับ
$user_data = ['labels' => [], 'data' => []];
foreach ($results as $row) {
    $user_data['labels'][] = $row['House'];
    $user_data['data'][] = (int) $row['booking_count'];
}
echo json_encode($user_data);
