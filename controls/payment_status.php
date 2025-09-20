<?php

header('Content-Type: application/json');
if (session_start() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['User_email'])) {
    echo json_encode(['message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}
include '../config/db_connect.php';
$charge_id = $_POST['charge_id'] ?? '';
$payment_status = $_POST['payment_status'] ?? '';
$qrcode = $_POST['qrCode'] ?? '';
$booking_id = $_POST['booking_id'] ?? '';
$booking_status = $_POST['booking_status'] ?? '';
// $payment_status = $_POST['payment_status'] ?? '';
// SQL บันทึกข้อมูล
$sql = "UPDATE booking 
SET Charge_id = :charge_id,
    Booking_status = :booking_status,
    Payment_gateway = :payment_gateway,
    Payment_status = :payment_status,
    Booking_qrcode = :qrcode
WHERE Booking_id = :booking_id
";
try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'charge_id' => $charge_id,
        'booking_status' => $booking_status,
        'payment_gateway' => 'Orcode',
        'payment_status' => $payment_status,
        'qrcode' => $qrcode,
        'booking_id' => $booking_id,
    ]);
    echo json_encode(['success' => true, 'message' => 'ชำระเงินสำเร็จ!']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}