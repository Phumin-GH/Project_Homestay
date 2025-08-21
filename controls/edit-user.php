<?php
session_start();
include '../config/db_connect.php';
if (empty($_SESSION['User_email'])) {
    header("Location: user-login.php");
    exit();
}

$message = '';
  $email = $_SESSION['User_email'];
if (isset($_PUT['save_edit'])) {
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
             $_SESSION['error'] = '<div class="alert alert-danger">ไม่พบข้อมูลผู้ใช้</div>';
        } else {
            
            $updateSQL = "UPDATE user SET Firstname = ?, Lastname = ?, Phone = ? WHERE User_email = ?";
            $stmt = $conn->prepare($updateSQL);
            $stmt->execute([$firstname, $lastname, $phone, $email]);

            // หากผู้ใช้กรอกรหัสผ่านปัจจุบันและต้องการเปลี่ยนรหัสผ่าน
            if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if (!password_verify($currentPassword, $host['User_password'])) {
                     $_SESSION['error'] = '<div class="alert alert-danger">รหัสผ่านปัจจุบันไม่ถูกต้อง</div>';
                } elseif ($newPassword !== $confirmPassword) {
                     $_SESSION['error'] = '<div class="alert alert-danger">รหัสผ่านใหม่ไม่ตรงกัน</div>';
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE user SET User_password = ? WHERE User_email = ?");
                    $stmt->execute([$hashedPassword, $email]);
                    $_SESSION['error1'] = '<div class="alert alert-success">อัปเดตรหัสผ่านและข้อมูลเรียบร้อย</div>';
                }
            } else {
                if (! $_SESSION['error1']) {
                     $_SESSION['error1'] = '<div class="alert alert-success">อัปเดตข้อมูลเรียบร้อย</div>';
                }
            }
            header("Location: ../users/profile.php");
                exit();
        }
    } catch (PDOException $e) {
         $_SESSION['error1'] = '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}
?>