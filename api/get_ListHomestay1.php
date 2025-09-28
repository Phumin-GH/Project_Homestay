<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Property.php';
require_once __DIR__ . '/../model/dao/Review.php';
$reviewsHandler = new Review($conn);
$propertyHandle = new Property($conn);
// บ้านพักที่อนุมัติแล้ว โชว์บน Menu
$homestay = $propertyHandle->show_House();



if (isset($_SESSION['Host_email'])) {
    $email = $_SESSION['Host_email'];
    $list_house = $propertyHandle->get_manageProperty($email);
    $properties = $propertyHandle->get_ListProperty($email);
    $total_properties = $propertyHandle->Total_properties($email);
}

//รับ house_id มาจาก main-menu.php
if (isset($_POST['house_id'])) {
    $property_id = $_POST['house_id'] ?? null;
    $property = null;
    $property = $propertyHandle->get_Property($property_id);
    $reviews = $reviewsHandler->get_Reviews($property_id);
    if (is_string($reviews)) {
        $_SESSION['err'] = "ไม่พบ ID";
        $reviews = [];
    }
    if (is_string($property)) {
        $_SESSION['err'] = $property;
    }
    // if (!empty($property['Property_lat']) && !empty($property['Property_lng'])) {
    //     $maps_url = "https://www.google.com/maps?q=" . $property['Property_lat'] . "," . $property['Property_lng'] . "&hl=th&z=16&output=embed";
    // }

    $images = $propertyHandle->get_Image($property_id);
    $rooms = $propertyHandle->get_rooms($property_id);
    $events = $propertyHandle->room_calendar($property_id);
    // $SESION['event'] = $events;
    // header("Location: detail_house.php");
    // exit();
}
//edit-property.php
if (isset($_POST['Property_id'])) {
    $property_id = $_POST['Property_id'];
    $house = $propertyHandle->showPropertys($property_id);
    $room = $propertyHandle->showRooms($property_id);
    if (!$room) {
        echo "<script>alert('บ้านพักที่เลือกไม่มีข้อมูลห้องพัก'); window.location.href='manage-property.php';</script>";
    }
}