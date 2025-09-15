<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Payment.php';
$paymetHandler = new Payment($conn);

// if (isset($_POST['booking_id']) && isset($_POST['charge_id'])) {
//     $booking_id = $_POST['booking_id'] ?? 0;
//     $charge_id = $_POST['charge_id'] ?? '';
//     $payment_status = $_POST['payment_status'] ?? '';
//     $qrcode = $_POST['qrCode'] ?? '';
//     // $booking_id = $_POST['booking_id'] ?? '';
//     $booking_status = $_POST['booking_status'] ?? '';
//     // $payment_status = $_POST['payment_status'] ?? '';
//     $result = $paymetHandler->update_payment_status($charge_id, $payment_status, $qrcode, $booking_id, $booking_status);
//     if ($result === true) {
//         echo json_encode(["success" => true, "message" => "ชำระเงินสำเร็จ!"]);
//     } elseif (is_string($result)) {
//         echo json_encode(["success" => false, "message" => $result]);
//     } else {
//         echo json_encode(["success" => false, "message" => $result]);
//     }
//     exit();
// }
if (isset($_POST['qrCode']) && isset($_POST['booking_id'])) {
    $qrCode = $_POST['qrCode'] ?? '';
    $charge_id = $_POST['charge_id'] ?? 0;
    $booking_id = $_POST['booking_id'] ?? 0;
    $status = $paymetHandler->CheckStatus($charge_id, $booking_id, $qrCode);
    if ($status === true) {
        echo json_encode(['success' => true, 'message' => 'ตรวจสอบและบันทึกข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => $status]);
    }
    exit();
} elseif (isset($_POST['amount']) && isset($_POST['number_card'])) {

    $booking_id = $_POST['booking_id'] ?? 0;
    $name = $_POST['Username'] ?? '';
    $number = $_POST['number_card'] ?? 0;
    $exMonth = $_POST['expMonth'] ?? 0;
    $exYear = $_POST['expYear'] ?? 0;
    $cvv = $_POST['cvv'] ?? 0;
    $amount = $_POST['amount'] ?? 0;
    $result = $paymetHandler->insertCreditCard($booking_id, $name, $number, $exMonth, $exYear, $cvv, $amount);
    if ($result === true) {
        echo json_encode(["success" => true, "message" => " Payment successful! "]);
    } else {
        echo json_encode(["success" => false, "message" => $result]);
    }
    exit();
} elseif (isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'] ?? 0;
    if (empty($booking_id)) {
        // return "ไม่พบ ID";
        echo json_encode(['success' => false, 'message' => "ไม่พบ ID"]);
    }
    $result = $paymetHandler->delete_Booking($booking_id);
    if ($result === true) {
        echo json_encode(['success' => true, 'message' => 'ลบการจองสำเร็จ!']);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'message' => "Invalid request"]);
    exit();
}


// if (isset($_GET['charge_id'])) {
//     $charge_id = $_GET['charge_id'] ?? '';
//     $status = $paymetHandler->CheckStatus($charge_id);
//     if (is_string($status)) {
//         $_SESSION['err'] = null;
//         $_SESSION['err'] = $status;
//         exit();
//     } else {

//         echo json_encode($status);
//         exit();
//     }
// }