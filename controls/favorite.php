<?php
session_start();
header("Content-Type: application/json");
include __DIR__ . '/../config/db_connect.php';

// ตรวจสอบ user login
if (!isset($_SESSION['User_email'])) {
    echo json_encode(["success" => false, "message" => "กรุณาเข้าสู่ระบบ"]);
    exit;
}

// ตรวจสอบว่ามี POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';
    $property_id = $_POST['property_id'] ?? null;

    if (!$property_id) {
        echo json_encode(["success" => false, "message" => "ไม่พบ property_id"]);
        exit;
    }

    $email = $_SESSION['User_email'];
    $stmt = $conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
    $stmt->execute([$email]);
    $user_id = $stmt->fetchColumn();

    try {
        if ($action === 'delete') {
            // ลบ favorite
            $check = $conn->prepare("SELECT 1 FROM favorite WHERE User_id = ? AND property_id = ?");
            $check->execute([$user_id, $property_id]);
            $exists = $check->fetchColumn();

            if ($exists) {
                $del = $conn->prepare("DELETE FROM favorite WHERE User_id = ? AND property_id = ?");
                $del->execute([$user_id, $property_id]);
                
                echo json_encode(["success" => true, "message" => "ลบข้อมูลใน favorites"]);
            } else {
                echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลใน favorites"]);
            }
            exit;
        } elseif ($action === 'toggle') {
            // เพิ่ม/ลบ favorite แบบ toggle
            $check = $conn->prepare("SELECT 1 FROM favorite WHERE User_id = ? AND property_id = ?");
            $check->execute([$user_id, $property_id]);
            $exists = $check->fetchColumn();

            if ($exists) {
                $del = $conn->prepare("DELETE FROM favorite WHERE User_id = ? AND property_id = ?");
                $del->execute([$user_id, $property_id]);
                echo json_encode(["success" => true, "action" => "removed"]);
            } else {
                $insert = $conn->prepare("INSERT INTO favorite (User_id, property_id) VALUES (?, ?)");
                $insert->execute([$user_id, $property_id]);
                echo json_encode(["success" => true, "action" => "added"]);
            }
            exit;
        } else {
            echo json_encode(["success" => false, "message" => "Action ไม่ถูกต้อง"]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(["success" => false, "message" => "Request ต้องเป็น POST"]);
    exit;
}
?>