<?php
// /api/chart_data.php

header('Content-Type: application/json');
require_once __DIR__ . '/model/config/db_connect.php'; // <<-- ตรวจสอบ Path ให้ถูกต้อง

$chartType = $_GET['type'] ?? ''; // รับประเภทของกราฟที่ต้องการ

$sql = '';
$data = ['labels' => [], 'data' => []];

// ใช้ switch case เพื่อเลือก Query ตามประเภทที่ร้องขอ
switch ($chartType) {
    // --- Case สำหรับ Bar Chart: ยอดขายรายเดือน ---
    case 'revenue':
        $sql = "
            SELECT DATE_FORMAT(Create_at, '%Y-%m') AS month, SUM(Total_price) AS total_value
            FROM booking
            WHERE Payment_status = 'paid' AND Create_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY month ORDER BY month ASC;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $dateObj = DateTime::createFromFormat('!Y-m', $row['month']);
            $data['labels'][] = $dateObj->format('F Y');
            $data['data'][] = (float)$row['total_value'];
        }
        break;

    // --- Case สำหรับ Line Chart: ผู้ใช้ใหม่รายวัน ---
    case 'users':
        $sql = "
            SELECT DATE(Create_at) AS day, COUNT(User_id) AS total_value
            FROM user
            WHERE Create_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY day ORDER BY day ASC;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $data['labels'][] = $row['day'];
            $data['data'][] = (int)$row['total_value'];
        }
        break;

    // --- Case สำหรับ Pie/Doughnut Chart: สัดส่วนช่องทางชำระเงิน ---
    case 'payments':
        $sql = "
            SELECT Payment_gateway AS label, COUNT(Booking_id) AS total_value
            FROM booking
            WHERE Payment_status = 'paid' AND Payment_gateway IS NOT NULL AND Payment_gateway != ''
            GROUP BY label ORDER BY total_value DESC;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as $row) {
            $data['labels'][] = ucfirst($row['label']);
            $data['data'][] = (int)$row['total_value'];
        }
        break;

    default:
        // ถ้าไม่มี type ที่ตรงกัน ให้ส่ง error กลับไป
        http_response_code(400);
        $data = ['error' => 'Invalid chart type specified.'];
        break;
}

echo json_encode($data);
