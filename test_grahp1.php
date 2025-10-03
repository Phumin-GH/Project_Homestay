<?php
// /api/get_monthly_revenue.php

// 1. ตั้งค่า Header ให้ส่งข้อมูลกลับไปเป็น JSON
header('Content-Type: application/json');

require_once __DIR__ . '/model/config/db_connect.php';

$stmt = $conn->prepare("
    SELECT
    b.Booking_id,
    r.Room_number, -- ดึง Room_number จากตาราง room
    b.Check_in,
    b.Check_out
FROM booking b
-- การ JOIN ตาราง room ควรจะอ้างอิงจากข้อมูลใน booking โดยตรง
LEFT JOIN room r ON b.Room_id = r.Room_id 
LEFT JOIN property p ON r.Property_id = p.Property_id -- เชื่อม property ผ่าน room จะดีกว่า
WHERE 
    b.Booking_status = 'successful' 
    AND p.Property_id = 1 AND (Check_status = 'Pending' OR Check_status = 'Checked_in')");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. แปลงข้อมูลให้อยู่ในรูปแบบ Event Object ที่ FullCalendar เข้าใจ
$events = [];
foreach ($bookings as $booking) {
    $events[] = [
        'id' => $booking['Booking_id'],
        'title' => 'จองแล้ว: ' . $booking['Room_number'], // ข้อความที่จะแสดงบนปฏิทิน
        'start' => $booking['Check_in'],                   // วันที่เริ่ม
        'end' => $booking['Check_out'],                  // วันที่สิ้นสุด (FullCalendar จะไม่รวมวันนี้)
        'color' => '#28a745',                              // (ตัวเลือก) กำหนดสีของ Event
    ];
}

// 5. ส่งข้อมูลกลับไปเป็น JSON
echo json_encode($events);
