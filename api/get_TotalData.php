<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/User.php';
require_once __DIR__ . '/../model/dao/Host.php';
require_once __DIR__ . '/../model/dao/Property.php';
require_once __DIR__ . '/../model/dao/Booking.php';
$userGet = new User($conn);
$hostGet = new Host($conn);
$propertyGet = new Property($conn);
$bookingGet = new Booking($conn);
$total_users = $userGet->total_user();
$total_hosts = $hostGet->total_host();
$total_properties = $propertyGet->total_property();
$total_bookings = $bookingGet->total_booking();
$total_income = $bookingGet->total_income();