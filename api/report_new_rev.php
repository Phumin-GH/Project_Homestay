<?php
// ตั้งค่า header ให้ถูกต้องและป้องกันการแคชข้อมูลเก่า
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

require_once __DIR__ . '/../model/config/db_connect.php';

try {


    echo json_encode($chart_data);
} catch (PDOException $e) {
    // กรณีเกิดข้อผิดพลาด ให้ส่งสถานะ Error กลับไป
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
