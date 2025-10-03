<?php
include("config/db_connect.php");
session_start();

if (!isset($_GET['id'])) {
    echo 'ไม่พบ ID';
    exit();
}

try {
    $email = "Fuck@gmail.com"; // โปรดเปลี่ยนเป็น session หรือค่าที่ปลอดภัย
    $sql = "SELECT * FROM user WHERE User_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $_SESSION['msg'] = 'User not found';
        header('Location: test02.php');
        exit();
    }

    $property_id = $_GET['id'];

    // ตรวจสอบว่ามีรายการ favorite นี้แล้วหรือยัง
    $stmt = $conn->prepare("SELECT COUNT(*) FROM favorite WHERE User_id = ? AND Property_id = ?");
    $stmt->execute([$user['User_id'], $property_id]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // ยังไม่มี favorite — เพิ่มเข้าไป
        $stmt = $conn->prepare("INSERT INTO favorite (User_id, Property_id) VALUES (?, ?)");
        $stmt->execute([$user['User_id'], $property_id]);
        $_SESSION['msg'] = 'Added to favorites';
    } else {
        // มี favorite แล้ว — ลบออก
        $stmt = $conn->prepare("DELETE FROM favorite WHERE User_id = ? AND Property_id = ?");
        $stmt->execute([$user['User_id'], $property_id]);
        $_SESSION['msg'] = 'Removed from favorites';
    }

    header('Location: test02.php');
    exit();

} catch (Exception $e) {
    $_SESSION['msg'] = 'Failed: ' . $e->getMessage();
    header('Location: test02.php');
    exit();
}
?>