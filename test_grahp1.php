<?php
header('Content-Type: application/json');
require_once __DIR__ . '/model/config/db_connect.php'; // ตรวจสอบให้แน่ใจว่า Path ถูกต้อง

// รับค่า 'period' จาก URL, ถ้าไม่ส่งมาให้ใช้ 'week' เป็นค่าเริ่มต้น
// $period = $_GET['period'] ?? 'week';
$period = $_POST['period'] ?? 'week';
$sql = "";

// สร้างเงื่อนไข SQL ตามช่วงเวลาที่เลือก
switch ($period) {
    case 'month':
        // ข้อมูล 30 วันย้อนหลัง, จัดกลุ่มข้อมูลรายวัน
        $sql = "SELECT DATE(Create_at) AS label, SUM(Total_price) AS value 
                FROM booking 
                WHERE Create_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND Booking_status = 'successful'
                GROUP BY label ORDER BY label ASC";
        break;
    case 'year':
        // ข้อมูล 12 เดือนย้อนหลัง, จัดกลุ่มข้อมูลรายเดือน
        $sql = "SELECT DATE_FORMAT(Create_at, '%Y-%m') AS label, SUM(Total_price) AS value 
                FROM booking
                WHERE Create_at >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND Booking_status = 'successful'
                GROUP BY label ORDER BY label ASC";
        break;
    case 'week':
    default:
        // ข้อมูล 7 วันย้อนหลัง, จัดกลุ่มข้อมูลรายวัน
        $sql = "SELECT DATE(Create_at) AS label, SUM(Total_price) AS value 
                FROM booking
                WHERE Create_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND Booking_status = 'successful'
                GROUP BY label ORDER BY label ASC";
        break;
}

try {
    $stmt = $conn->query($sql);
    $dataFromDb = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // จัดรูปแบบข้อมูลให้เป็น labels และ data สำหรับ Chart.js
    $labels = [];
    $data = [];
    foreach ($dataFromDb as $row) {
        $labels[] = $row['label'];
        $data[] = (float)$row['value'];
    }

    // ส่งข้อมูลกลับไปเป็น JSON
    echo json_encode(['labels' => $labels, 'data' => $data]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}