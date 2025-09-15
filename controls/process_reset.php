<?php
header('Content-Type: application/json');
require '../config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $token = $_POST['token'] ?? '';
    // ตรวจสอบช่องว่าง
    if (empty($password) || empty($confirm) || empty($token)) {
        echo json_encode(["success" => false, "message" => "Missing required fields: " . implode(", ", array_filter([$password, $confirm, $token]))]);
        exit();
    }

    // ตรวจสอบ password ตรงกัน
    if ($password !== $confirm) {
        echo json_encode(["success" => false, "message" => "Passwords do not match!"]);
        exit();
    }

    // ตรวจสอบ token ใน DB
    // (สมมติว่ามี table reset_tokens เก็บ user_id, token, expiry)

    $stmt = $conn->prepare("SELECT User_id FROM user WHERE Token = ? AND Expires_at > NOW()");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $conn->prepare("SELECT Host_id FROM host WHERE Token = ? AND Expires_at > NOW()");
    $stmt->execute([$token]);
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && isset($row['User_id'])) {
        // อัปเดตรหัสผ่านใหม่
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $conn->prepare("UPDATE user SET User_password = ?, Token = NULL, Expires_at = NULL WHERE User_id = ?");
            $stmt->execute([$hashed, $row['User_id']]);


            echo json_encode(["success" => true, "message" => "Password updated successfully!", "reddit" => "users/user-login.php"]);
        } catch (PDOException $e) {
            // โค้ดใน catch จะทำงานเมื่อเกิด error เช่น column ไม่ถูกต้อง, connection error
            echo json_encode([
                "success" => false,
                "message" => "Database error: " . $e->getMessage(),

            ]);
            exit();
        }
    } elseif ($rows && isset($rows['Host_id'])) {
        // อัปเดตรหัสผ่านใหม่
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        try {
            $stmt = $conn->prepare("UPDATE host SET Host_password = ?, Token = NULL, Expires_at = NULL WHERE Host_id = ?");
            $stmt->execute([$hashed, $rows['Host_id']]);


            echo json_encode(["success" => true, "message" => "Password updated successfully!", "reddit" => "hosts/host-login.php"]);
        } catch (PDOException $e) {
            // โค้ดใน catch จะทำงานเมื่อเกิด error เช่น column ไม่ถูกต้อง, connection error
            echo json_encode([
                "success" => false,
                "message" => "Database error: " . $e->getMessage(),

            ]);
            exit();
        }
    } elseif (!$row && !$rows) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid token or token expired.",
        ]);
    }
}
