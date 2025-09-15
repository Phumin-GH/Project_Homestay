<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Payment.php';
$paymetHandler = new Payment($conn);
$errorMessage = null;
$booking_id = $_SESSION['booking_id'] ?? 0;
if (isset($_SESSION['User_email'])) {
    $email = $_SESSION['User_email'];
    $result = $paymetHandler->getBooking($email,  $booking_id);
}

if (isset($_SESSION['method'])) {
    $method = $_SESSION['method'] ?? 0;
    $total_price = $_SESSION['total_price'] ?? 0;

    $qrcode = $paymetHandler->generateQRcode($total_price, $booking_id, $method);
    if (is_array($qrcode) && isset($qrcode['object']) && $qrcode['object'] === 'charge') {
        $charge_id = $qrcode['id'];
        $qr_code_url = $qrcode['source']['scannable_code']['image']['download_uri'];
        $booking_id = $qrcode['booking_Id'];
    } else {
        // ตรวจสอบว่าผลลัพธ์เป็น String หรือไม่
        if (is_string($qrcode)) {
            $errorMessage = $qrcode;
        }
    }
} else {
    $errorMessage = "ไม่พบข้อมูลช่องทางการชำระเงินใน Session";
}
// set expire 5 นาที
$expires_at = time() + 300;
$expires_at_iso8601 = date('c', $expires_at);
$expires_at_timestamp = strtotime($expires_at_iso8601) * 1000;
