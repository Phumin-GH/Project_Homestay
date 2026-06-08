<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION["User_email"])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบก่อน']);
    exit();
}

require_once __DIR__ . '/../model/config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $property_id = (int) ($_POST['property_id'] ?? 0);
    $user_id = (int) ($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if (!$property_id || !$user_id || !in_array($action, ['add', 'remove'])) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit();
    }

    // ตรวจสอบว่า user_id ตรงกับ session หรือไม่
    $userStmt = $conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
    $userStmt->execute([$_SESSION['User_email']]);
    $userData = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData || $userData['User_id'] != $user_id) {
        echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
        exit();
    }

    if ($action === 'add') {
        // เพิ่มใน favorites
        // ตรวจสอบว่ามีอยู่แล้วหรือไม่
        $checkStmt = $conn->prepare("SELECT Favorite_id FROM favorite WHERE User_id = ? AND Property_id = ?");
        $checkStmt->execute([$user_id, $property_id]);

        if ($checkStmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'มีในรายการโปรดอยู่แล้ว']);
            exit();
        }

        $insertStmt = $conn->prepare("INSERT INTO favorite (User_id, Property_id) VALUES (?, ?)");
        $result = $insertStmt->execute([$user_id, $property_id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'เพิ่มในรายการโปรดแล้ว']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถเพิ่มในรายการโปรดได้']);
        }

    } else { // remove
        $deleteStmt = $conn->prepare("DELETE FROM favorite WHERE User_id = ? AND Property_id = ?");
        $result = $deleteStmt->execute([$user_id, $property_id]);

        if ($result) {
            echo json_encode(['success' => true, 'message' => 'ลบออกจากรายการโปรดแล้ว']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบออกจากรายการโปรดได้']);
        }
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>