<?php
header('Content-Type: application/json');

// จำลองข้อมูลห้องพัก (ในกรณีจริงควรดึงจากฐานข้อมูล)
$homestays = [
    1 => ['name' => 'ห้อง A1', 'price_per_night' => 1000, 'base_guests' => 2],
    2 => ['name' => 'ห้อง B1', 'price_per_night' => 1500, 'base_guests' => 2],
    3 => ['name' => 'ห้อง C1', 'price_per_night' => 2000, 'base_guests' => 4]
];

// จำลองโค้ดส่วนลด (ในกรณีจริงควรดึงจากฐานข้อมูล)
$discount_codes = [
    'SAVE888' => ['discount_amount' => 0.1, 'valid_until' => '2025-12-31'],
    'SAVE999' => ['discount_amount' => 0.18, 'valid_until' => '2025-12-31']
];

// รับข้อมูลจาก AJAX
$homestay_id = (int)($_POST['homestay_id'] ?? 0);
$check_in_date = $_POST['check_in_date'] ?? '';
$nights = (int)($_POST['nights'] ?? 0);
$guests = (int)($_POST['guests'] ?? 0);
$discount_code = trim($_POST['discount_code'] ?? '');

// ตรวจสอบข้อมูล
if ($homestay_id <= 0 || !isset($homestays[$homestay_id])) {
    echo json_encode(['error' => 'กรุณาเลือกห้องพัก']);
    exit;
}
if (empty($check_in_date)) {
    echo json_encode(['error' => 'กรุณาเลือกวันที่เช็คอิน']);
    exit;
}
if ($nights <= 0) {
    echo json_encode(['error' => 'จำนวนคืนต้องมากกว่า 0']);
    exit;
}
if ($guests <= 0) {
    echo json_encode(['error' => 'จำนวนผู้เข้าพักต้องมากกว่า 0']);
    exit;
}

// ตรวจสอบว่าวันที่เช็คอินอยู่ในอนาคต
$today = date('Y-m-d');
if ($check_in_date < $today) {
    echo json_encode(['error' => 'วันที่เช็คอินต้องเป็นวันในอนาคต']);
    exit;
}

// คำนวณราคา
$price_per_night = $homestays[$homestay_id]['price_per_night'];
$base_guests = $homestays[$homestay_id]['base_guests'];
$extra_guest_fee = 200; // ค่าธรรมเนียมผู้เข้าพักเพิ่มต่อคนต่อคืน
$service_fee = 100; // ค่าบริการ

$total_price = $nights * $price_per_night;

// เพิ่มค่าธรรมเนียมผู้เข้าพักเพิ่ม
if ($guests > $base_guests) {
    $total_price += ($guests - $base_guests) * $extra_guest_fee * $nights;
}


// เพิ่มค่าบริการ
$total_price += $service_fee;

// ตรวจสอบโค้ดส่วนลด
$discount = 0;
$message = '';
if (!empty($discount_code)) {
    if (isset($discount_codes[$discount_code]) && $discount_codes[$discount_code]['valid_until'] >= $today) {
        $discount = $discount_codes[$discount_code]['discount_amount'];
        $total_price = $total_price-($total_price * $discount);
        $message = 'ใช้ส่วนลดสำเร็จ: ลด ' . number_format($discount, 2) . ' บาท';
    } else {
        $message = 'โค้ดส่วนลดไม่ถูกต้องหรือหมดอายุ';
    }
}

// ตรวจสอบว่าราคาไม่ติดลบ
if ($total_price < 0) {
    $total_price = 0;
}

// ส่งผลลัพธ์กลับในรูปแบบ JSON
echo json_encode([
    'total_price' => round($total_price, 2),
    'message' => $message
]);

?>