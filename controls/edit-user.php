<?php
session_start();
include '../config/db_connect.php';
if (empty($_SESSION['User_email'])) {
    header("Location: user-login.php");
    exit();
}

$message = '';
$email = $_SESSION['User_email'];
if (empty($_SESSION['User_email'])) {
    header("Location: user-login.php");
    exit();
}
if (isset($_SESSION['User_email'])) {
    $email = $_SESSION['User_email'];
    $sql = "SELECT * FROM user WHERE User_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $users = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (isset($_POST['save_edit'])) {

    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
    try {
        // ตรวจสอบว่าผู้ใช้มีอยู่จริง
        $stmt = $conn->prepare("SELECT * FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$host) {
            echo json_encode(["success" => false, "message" => "ไม่พบข้อมูลผู้ใช้"]);
            exit();
        } else {
            $updateSQL = "UPDATE user SET Firstname = ?, Lastname = ?, Phone = ? WHERE User_email = ?";
            $stmt = $conn->prepare($updateSQL);
            $stmt->execute([$firstname, $lastname, $phone, $email]);
            // หากผู้ใช้กรอกรหัสผ่านปัจจุบันและต้องการเปลี่ยนรหัสผ่าน
            if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if (!password_verify($currentPassword, $host['User_password'])) {
                    echo json_encode(["success" => false, "message" => "รหัสผ่านปัจจุบันไม่ถูกต้อง"]);
                    exit();
                } elseif ($newPassword !== $confirmPassword) {
                    echo json_encode(["success" => false, "message" => "รหัสผ่านใหม่ไม่ตรงกัน"]);
                    exit();
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE user SET User_password = ? WHERE User_email = ?");
                    $stmt->execute([$hashedPassword, $email]);
                    echo json_encode(["success" => true, "message" => "อัปเดตรหัสผ่านและข้อมูลเรียบร้อย"]);
                    exit();
                }
            } else {
                echo json_encode(["success" => true, "message" => "อัปเดตข้อมูลเรียบร้อย"]);
                exit();
            }
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Database Errors:" . $e->getMessage()]);
        exit();
    }
}
