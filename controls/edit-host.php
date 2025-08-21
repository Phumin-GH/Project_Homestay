<?php
session_start();
include '../config/db_connect.php';
if (empty($_SESSION['Host_email'])) {
    header("Location: host-login.php");
    exit();
}

$message = '';
  $email = $_SESSION['Host_email'];
if (isset($_POST['save_edit'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
  

    try {
        // ตรวจสอบว่าผู้ใช้มีอยู่จริง
        $stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ?");
        $stmt->execute([$email]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$host) {
            $message = '<div class="alert alert-danger">ไม่พบข้อมูลผู้ใช้</div>';
        } else {
            
            $updateSQL = "UPDATE host SET Host_firstname = ?, Host_lastname = ?, Host_phone = ? WHERE Host_email = ?";
            $stmt = $conn->prepare($updateSQL);
            $stmt->execute([$firstname, $lastname, $phone, $email]);

            // หากผู้ใช้กรอกรหัสผ่านปัจจุบันและต้องการเปลี่ยนรหัสผ่าน
            if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if (!password_verify($currentPassword, $host['Host_password'])) {
                    $message = '<div class="alert alert-danger">รหัสผ่านปัจจุบันไม่ถูกต้อง</div>';
                } elseif ($newPassword !== $confirmPassword) {
                    $message = '<div class="alert alert-danger">รหัสผ่านใหม่ไม่ตรงกัน</div>';
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE host SET Host_password = ? WHERE Host_email = ?");
                    $stmt->execute([$hashedPassword, $email]);
                    $message = '<div class="alert alert-success">อัปเดตรหัสผ่านและข้อมูลเรียบร้อย</div>';
                }
            } else {
                if (!$message) {
                    $message = '<div class="alert alert-success">อัปเดตข้อมูลเรียบร้อย</div>';
                }
            }
            header("Location: ../hosts/profile.php");
                exit();
        }
    } catch (PDOException $e) {
        $message = '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>