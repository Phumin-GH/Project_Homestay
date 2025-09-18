<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Booking.php';
$checkHandler = new Booking($conn);
if (isset($_POST['Property_id'])) {
    $property_id = $_POST['Property_id'];
    $check_in = $checkHandler->get_check_in($property_id);
    $pending = $checkHandler->get_pending($property_id);
    $check_out = $checkHandler->get_check_out($property_id);
}
