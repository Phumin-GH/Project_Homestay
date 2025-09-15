<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Payment.php';

$paymetHandler = new Payment($conn);

$errorMessage = null;

$booking_id = $_SESSION['booking_id'] ?? 0;
$method = $_SESSION['method'] ?? 0;
$total_price = $_SESSION['total_price'] ?? 0;
if (isset($_SESSION['User_email'])) {
    $email = $_SESSION['User_email'];
    $result = $paymetHandler->pushBooking($email,  $booking_id);
}

if (isset($_SESSION['method'])) {

    if ($total_price <= 0 || $booking_id <= 0 || empty($method)) { // แก้ไขเงื่อนไขเล็กน้อย
        $errorMessage = "ข้อมูลสำหรับการชำระเงินไม่ครบถ้วน";
    }
    $qrcode = $paymetHandler->generateQRcode($total_price, $booking_id, $method);
    if (is_string($qrcode) && isset($qrcode['object']) && $qrcode['object'] === 'charge') {
        $errorMessage = $qrcode;
    }
} else {
    $errorMessage = "ไม่พบข้อมูลช่องทางการชำระเงินใน Session";
}
// set expire 20 นาที
$expires_at = time() + 1200;
$expires_at_iso8601 = date('c', $expires_at);
$expires_at_timestamp = strtotime($expires_at_iso8601) * 1000;
