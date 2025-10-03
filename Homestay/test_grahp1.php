<?php
// /api/get_monthly_revenue.php

// 1. ตั้งค่า Header ให้ส่งข้อมูลกลับไปเป็น JSON
header('Content-Type: application/json');

require_once __DIR__ .'/model/config/db_connect.php';

// 3. เขียน SQL Query เพื่อสรุปยอดขายในแต่ละเดือน
// ดึงข้อมูลย้อนหลัง 12 เดือน
$sql = "
    SELECT 
        DATE_FORMAT(Create_at, '%Y-%m') AS month,
        SUM(Total_price) AS total_revenue
    FROM 
        booking
    WHERE 
        Payment_status = 'paid' AND Create_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH) AND Property_id = 1
    GROUP BY 
        DATE_FORMAT(Create_at, '%Y-%m')
    ORDER BY 
        month ASC;
";

$stmt = $conn->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. เตรียมข้อมูลสำหรับ Chart.js
$chart_data = [
    'labels' => [], // แกน X (ชื่อเดือน)
    'data' => []   // แกน Y (ยอดขาย)
];

foreach ($results as $row) {
    // แปลง '2025-09' ให้เป็น 'September 2025'
    $dateObj = DateTime::createFromFormat('!Y-m', $row['month']);
    $chart_data['labels'][] = $dateObj->format('F Y'); 
    
    $chart_data['data'][] = (float)$row['total_revenue'];
}

// 5. ส่งข้อมูลกลับไปเป็น JSON
echo json_encode($chart_data);

?>