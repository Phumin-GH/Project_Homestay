<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
include_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Property.php';
$propertyHandle = new Property($conn);
if (isset($_GET['Property_id'])) {
    $property_id = $_GET['Property_id'];
    $room = $propertyHandle->get_RoomsWalkin($property_id);
    echo json_encode($room);
    exit();
}
