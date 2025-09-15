<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Property.php';

$propertyHandle = new Property($conn);

$list_house = $propertyHandle->get_manageProperty($email);
if ($list_house === false) {
    $_SESSION['err'] = "ไม่พบข้อมูลอีเมล";
}

// บ้านพักที่อนุมัติแล้ว โชว์บน Menu
$homestay = $propertyHandle->show_House();

//รับ house_id มาจาก main-menu.php
if (isset($_POST['house_id'])) {
    $property_id = $_POST['house_id'] ?? null;
    $property = $propertyHandle->get_Property($property_id);
    if (is_string($property)) {
        $_SESSION['err'] = $property;
    }
    if (!empty($property['Property_lat']) && !empty($property['Property_lng'])) {
        $maps_url = "https://www.google.com/maps?q=" . $property['Property_lat'] . "," . $property['Property_lng'] . "&hl=th&z=16&output=embed";
    }
    $images = $propertyHandle->get_Image($property_id);
    $rooms = $propertyHandle->get_rooms($property_id);
}
// $email = $_SESSION['Host_email'];
if (isset($_POST['Property_id'])) {
    $property_id = $_POST['Property_id'];
    $house = $propertyHandle->showPropertys($property_id);
    $room = $propertyHandle->showRooms($property_id);
    if (!$room) {
        // No property found
        echo "<script>alert('บ้านพักที่เลือกไม่มีข้อมูลห้องพัก'); window.location.href='manage-property.php';</script>";
    }
}