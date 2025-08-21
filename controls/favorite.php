<?php
session_start();
header("Content-Type: application/json"); // ใช้ application/json เพราะเราส่ง/รับเป็น JSON
include __DIR__ . '/../config/db_connect.php';

//  เช็คว่ามี user login ไหม
if (!isset($_SESSION['User_email'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบ"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

//  ป้องกัน error ถ้าไม่ได้ส่ง house_id มา
if (!isset($data['house_id'])) {
    echo json_encode(["success" => false, "message" => "ไม่พบ house_id"]);
    exit;
}

$house_id = $_POST['house_id'];
$email = $_SESSION['User_email']; // ระวังตัวพิมพ์เล็ก/ใหญ่ ต้องตรงกัน
$stmt = $conn->prepare("SELECT User_id FROM user WHERE email = ?");
$stmt->execute([$email]);
$user_id = $stmt->fetchColumn();

try {
    //  ตรวจสอบว่ามี favorite อยู่แล้วหรือไม่
    $check = $conn->prepare("SELECT 1 FROM favorite WHERE User_id = ? AND property_id = ?");
    $check->execute([$user_id, $house_id]);
    $exists = $check->fetchColumn();

    if ($exists) {
        //  ถ้ามีแล้ว → ลบ
        $del = $conn->prepare("DELETE FROM favorite WHERE User_id = ? AND property_id = ?");
        $del->execute([$user_id, $house_id]);
        echo json_encode(["success" => true, "action" => "removed"]);
    } else {
        //  ถ้ายังไม่มี → เพิ่ม
        $insert = $conn->prepare("INSERT INTO favorite (User_id, property_id) VALUES (?, ?)");
        $insert->execute([$user_id, $house_id]);
        echo json_encode(["success" => true, "action" => "added"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}