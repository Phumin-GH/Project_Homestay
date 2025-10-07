<?php
header('Content-Type: application/json');
require '../model/config/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $token = $_POST['token'] ?? '';
    if (empty($password) || empty($confirm) || empty($token)) {
        echo json_encode(["success" => false, "message" => "กรอกข้อมูลไม่ครบ: " . implode(", ", array_filter([$password, $confirm]))]);
        exit();
    }
    if ($password !== $confirm) {
        echo json_encode(["success" => false, "message" => "รหัสผ่านไม่ตรงกัน !"]);
        exit();
    }
    try {
        $stmt = $conn->prepare("SELECT User_id FROM user WHERE Token = ? AND Expires_at > NOW()");
        $stmt->execute([$token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && isset($row['User_id'])) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            try {
                $stmt = $conn->prepare("UPDATE user SET User_password = ?, Token = NULL, Expires_at = NULL WHERE User_id = ?");
                $stmt->execute([$hashed, $row['User_id']]);
                echo json_encode(["success" => true, "message" => "อัปเดตรหัสผ่านสำเร็จ!", "reddit" => "users/user-login.php"]);
                exit();
            } catch (PDOException $e) {
                // โค้ดใน catch จะทำงานเมื่อเกิด error เช่น column ไม่ถูกต้อง, connection error
                echo json_encode([
                    "success" => false,
                    "error" => "Database error: " . $e->getMessage(),
                    "reddit" => "users/user-login.php"
                ]);
                exit();
            }
        }
        $stmt = $conn->prepare("SELECT Host_id FROM host WHERE Token = ? AND Expires_at > NOW()");
        $stmt->execute([$token]);
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($rows && isset($rows['Host_id'])) {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            try {
                $stmt = $conn->prepare("UPDATE host SET Host_password = ?, Token = NULL, Expires_at = NULL WHERE Host_id = ?");
                $stmt->execute([$hashed, $rows['Host_id']]);
                echo json_encode(["success" => true, "message" => "อัปเดตรหัสผ่านสำเร็จ!", "reddit" => "hosts/host-login.php"]);
                exit();
            } catch (PDOException $e) {
                // โค้ดใน catch จะทำงานเมื่อเกิด error เช่น column ไม่ถูกต้อง, connection error
                echo json_encode([
                    "success" => false,
                    "error" => "Database error: " . $e->getMessage(),
                    "reddit" => "hosts/host-login.php"
                ]);
                exit();
            }
        }
    } catch (PDOException $e) {
        echo json_encode([
            "success" => false,
            "error" => "Database error: " . $e->getMessage(),
        ]);
        exit();
    }
}
