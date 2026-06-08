<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
include_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Property.php';
$propertyHandle = new Property($conn);
if (isset($_GET['id'])) {
    $property_id = $_GET['id'];
    $check_in = $_GET['check_in'];
    $check_out = $_GET['check_out'];
    $room = $propertyHandle->get_RoomsWalkin($property_id, $check_in, $check_out);
    echo json_encode($room);
    exit();
}