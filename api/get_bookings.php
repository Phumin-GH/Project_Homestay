<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Booking.php';
$bookHandler = new Booking($conn);
if (isset($_SESSION['User_email'])) {
    $email = $_SESSION['User_email'];
    $errmsg = null;
    $bookings = $bookHandler->get_Booking($email);
    $history_booking = $bookHandler->get_HistoryBook($email);
    if ($bookings === false || $history_booking === false) {
        $errmsg = "ไม่พบข้อมูลการจอง";
        $bookings = [];
        $history_booking = [];
    } else {
        $bookings;
        $history_booking;
    }
}
