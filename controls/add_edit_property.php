<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Property.php';
if (empty($_SESSION['Host_email'])) {
    echo json_encode(["success" => false, "message" => "Pls Login"]);
    // header("Location: host-login.php");
    exit();
}
$propertyHandle = new Property($conn);
if (isset($_POST['add_property'])) {
    $errors = [];
    $house_name = trim($_POST['house_name']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $subdistrict = trim($_POST['subdistrict']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $roomNums = $_POST['roomNum'] ?? [];
    $roomPrices = $_POST['roomPrice'] ?? [];
    $roomCaps = $_POST['roomCap'] ?? [];
    $roomUtens = $_POST['roomUten'] ?? [];
    $add_Property = $propertyHandle->add_Property($house_name, $province, $district, $subdistrict, $latitude, $longitude, $roomNums, $roomPrices, $roomCaps, $roomUtens);
    if ($add_Property === true) {
        echo json_encode(["success" => true, "message" => "ส่งข้อมูลแล้ว รอแอดมินอนุมัติ"]);
        exit();
    } else {
        echo json_encode(["success" => false, "message" => $add_Property]);
        exit();
    }
}

if (isset($_POST['edit_property'])) {
    $errors = [];
    $property_id = trim($_POST['property_id']);
    $house_name = trim($_POST['house_name']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $subdistrict = trim($_POST['subdistrict']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $roomIds = $_POST['roomId'] ?? [];
    $roomNums = $_POST['roomNum'] ?? [];
    $roomPrices = $_POST['roomPrice'] ?? [];
    $roomCaps = $_POST['roomCap'] ?? [];
    $roomUtens = $_POST['roomUten'] ?? [];
    $status_room = $_POST['selectValue'] ?? [];
    $edit_property = $propertyHandle->edit_Property($property_id, $house_name, $province, $district, $subdistrict, $latitude, $longitude, $roomIds, $roomNums, $roomPrices, $roomCaps, $roomUtens, $status_room);
    if ($edit_property === true) {
        echo json_encode(['success' => true, 'message' => "แก้ไขข้อมูลบ้านพักเรียบร้อยแล้ว"]);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $edit_property]);
        exit();
    }
}
// $json_data = file_get_contents("php://input");
// $data = json_decode($json_data, true);
// if (isset($data['del_room'])) {
//     $room_id = $data['room_id'];
//     $room = "SELECT COUNT(*) FROM room WHERE Room_id = ?";
//     $stmt = $conn->prepare($room);
//     $stmt->execute([$room_id]);
//     $c_room = $stmt->fetchColumn();
//     if ($c_room > 0) {
//         $sql = "DELETE FROM room WHERE Room_id = ? ";
//         $stmt = $conn->prepare($sql);
//         $stmt->execute([$c_room]);
//         echo json_encode(["success" => true, "message" => "ลบเรียบร้อย"]);
//     } else {
//         echo json_encode(["success" => false, "message" => "ไม่พบหมายเลขห้องที่ต้องการลบ"]);
//     }
// }