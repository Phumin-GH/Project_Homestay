<?php
if (session_start() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Refund.php';

$RefundHandler = new Refund($conn);
if (isset($_SESSION["User_email"])) {
    if (isset($_POST['reason'])) {
        $reason = $_POST['reason'] ?? '';
        $booking_id = (int)($_POST['booking_id'] ?? 0);
        $amount = (int)($_POST['amount'] ?? 0);
        $email = $_SESSION["User_email"];
        $result = $RefundHandler->submit_refund($email, $booking_id, $reason, $amount);
        if ($result === true) {
            echo json_encode(['success' => true, 'message' => 'ยื่นเรื่องขอคืนเงินเรียบร้อยแล้ว']);
        } elseif (is_string($result)) {
            echo json_encode(['success' => false, 'message' => $result]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถยื่นเรื่องได้']);
        }
        exit;
    }
    if (isset($_POST['booking_id'])) {
        $booking_id = (int)($_POST['booking_id'] ?? 0);
        $refund = $RefundHandler->cancel_booking($booking_id);
        if ($refund) {
            echo json_encode(['success' => true, 'data' => $refund]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลการคืนเงิน']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Pls Login']);
    exit();
}
