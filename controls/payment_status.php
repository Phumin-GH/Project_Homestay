<?php

// เชื่อมต่อฐานข้อมูล MySQL (ตัวอย่าง PDO)
ob_start();
header('Content-Type: application/json');
session_start();

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
$payment_status = $_POST['payment_status'] ?? '';

// SQL บันทึกข้อมูล
$sql = "
UPDATE booking 
SET Charge_id = :charge_id,
    Booking_status = :booking_status,
    Payment_status = :payment_status,
    Booking_slip = :qrcode
WHERE Booking_id = :booking_id
";


try {
    $stmt = $conn->prepare($sql);
    $stmt->execute([
    'booking_id' => $booking_id,
    'charge_id' => $charge_id,
    'booking_status' => $booking_status,
    'payment_status' => $payment_status,
    'qrcode' => $qrcode]);
    echo json_encode(['success'=>true, 'message' => 'ชำระเงินสำเร็จ!']);
} catch(PDOException $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}