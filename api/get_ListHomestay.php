<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Property.php';
require_once __DIR__ . '/../model/dao/Review.php';
require_once __DIR__ . '/../model/dao/Payment.php';
$reviewsHandler = new Review($conn);
$propertyHandle = new Property($conn);
$paymentHandle = new Payment($conn);
// บ้านพักที่อนุมัติแล้ว โชว์บน Menu
$homestay = $propertyHandle->show_House();



if (!empty($_SESSION['Host_email'])) {
    $email = $_SESSION['Host_email'];
    $list_house = $propertyHandle->get_manageProperty($email);
    $properties = $propertyHandle->get_ListProperty($email);
    $total_properties = $propertyHandle->Total_properties($email);
    $total_bookings = $propertyHandle->Total_booking($email);
    $total_income = $propertyHandle->Total_income($email);
    $total_reviews = $propertyHandle->Total_reviews($email);
}
if (!empty($_SESSION['Admin_email'])) {
    $AllProperty = $propertyHandle->get_AllProperty();
}

//รับ house_id มาจาก main-menu.php
if (isset($_POST['house_id'])) {

    $property_id = (int) ($_POST['house_id'] ?? null);
    $review_id = (int) ($_POST['review_id'] ?? null);
    $property = null;
    $property = $propertyHandle->get_Property($property_id);
    $reviews = $reviewsHandler->get_Reviews($property_id);
    $all_reviews = $reviewsHandler->get_All_Reviews($property_id);
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