<?php
if (session_start() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Refund.php';
require_once __DIR__ . '/../model/dao/Payment.php';
$PaymentHandler = new Payment($conn);
$RefundHandler = new Refund($conn);

if (isset($_SESSION['Host_email']) && isset($_POST['host_approve'])) {
    if (isset($_POST['booking_id'])) {

        $refund_id = (int)($_POST['booking_id'] ?? 0);
        if (isset($_POST['host_approve'])) {
            $result = $RefundHandler->approve_refund($refund_id);
            $_SESSION['msg'] = "อนุมัติคำขอคืนเงินเรียบร้อยแล้ว";
        } elseif (isset($_POST['host_reject'])) {
            $result = $RefundHandler->reject_refund($refund_id);
            $_SESSION['msg'] = "ยกเลิกคำขอคืนเงินเรียบร้อยแล้ว";
        } else {
            $_SESSION['msg'] = "ไม่สามารถดำเนินการได้";
        }
        header('Location: ../views/hosts/refund_booking.php');
        exit;
    } else {
        header('Location: ../views/hosts/refund_booking.php');
        exit;
    }
}
if (isset($_SESSION['Admin_email']) && isset($_POST['booking_id'])) {
    $booking_id = (int)($_POST['booking_id'] ?? 0);
    if (isset($_POST['approve'])) {
        $result = $PaymentHandler->Submit_refund($booking_id);
        if ($result === true) {
            $_SESSION['msg'] = "อนุมัติคำขอคืนเงินเรียบร้อยแล้ว";
        } else {
            $_SESSION['msg'] = $result;
        }
    }
    // elseif (isset($_POST['cancel'])) {
    //     $result = $PaymentHandler->cancel_refund($booking_id);
    //     $_SESSION['msg'] = "ยกเลิกคำขอคืนเงินเรียบร้อยแล้ว";
    // } else {
    //     $_SESSION['msg'] = "ไม่สามารถดำเนินการได้";
    // }
    header('Location: ../views/admin/manage-refund.php');
    exit();
}
