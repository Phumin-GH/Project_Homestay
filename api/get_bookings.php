<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Booking.php';
$bookHandler = new Booking($conn);
if (isset($_SESSION['User_email'])) {
    $email = $_SESSION['User_email'];
    $errmsg = null;
    $bookings = [];
    $history_booking = [];
    $cancel_booking = [];
    // $bookings = $bookHandler->get_Booking($email);
    $history_booking = $bookHandler->get_HistoryBook($email);
    $cancel_booking = $bookHandler->get_CancelBook($email);
    if (empty($bookings) && empty($history_booking) && empty($cancel_booking)) {
        $errmsg = "ไม่พบข้อมูลการจองทั้งหมด";
    }
}
