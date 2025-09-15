<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header("Content-Type: application/json");
require_once  __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Favorites.php';
// ตรวจสอบ user login
if (!isset($_SESSION['User_email'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบ"]);
    exit;
}
$favoriteHandler = new Favorites($conn);
// ตรวจสอบว่ามี POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $property_id = $_POST['property_id'] ?? null;
    if (!$property_id) {
        echo json_encode(["success" => false, "message" => "ไม่พบ property_id"]);
        exit;
    }
    $email = $_SESSION['User_email'];
    if ($action === 'toggle') {
        $result = $favoriteHandler->addFavorites($email, $property_id);
        echo json_encode(["success" => true, "action" => "added"]);
    } elseif ($action === 'delete') {
        $result = $favoriteHandler->removeFavorites($email, $property_id);
        echo json_encode(["success" => true, "action" => "removeed"]);
    } else {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลการทำรายการ"]);
    }
}
