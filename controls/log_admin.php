<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/db_connect.php'; // ต้องแน่ใจว่า $conn คือ PDO instance

$errors = [];


if (isset($_POST['admin_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่าไม่ว่าง
    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in both email and password.";
        $_SESSION['error'] = "กรุณากรอกอีเมลและรหัสผ่าน";
         header("Location: ../admin/admin-login.php");
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM admin_sys WHERE Admin_email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Admin_password'])) {
                // ล็อกอินสำเร็จ
                $_SESSION['Admin_email'] = $user['Admin_email'];
                $_SESSION['message'] = "Login เรียบร้อย";
                header("Location: ../admin/admin-dashboard.php");
                exit();
            } else {
                $_SESSION['error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
                header("Location: ../admin/admin-login.php");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            $_SESSION['message'] = "Login ไม่ได้";
            header("Location: ../users/user-login.php?tab=login");
            exit();
        }
    }
}

if (empty($_SESSION['User_email'])) {
header("Location: user-login.php");
exit();
}
if(isset($_SESSION['Admin_email'])){
    $email = $_SESSION['Admin_email'];
    $sql ="SELECT * FROM admin_sys WHERE Admin_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

}
if (isset($_PUT['save_edit'])) {

    $firstname = trim($_POST['username']);
    
    $phone = trim($_POST['phone']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
  

    try {
        // ตรวจสอบว่าผู้ใช้มีอยู่จริง
        $stmt = $conn->prepare("SELECT * FROM admin_sys WHERE Admin_email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$host) {
             $_SESSION['error'] = '<div class="alert alert-danger">ไม่พบข้อมูลผู้ใช้</div>';
        } else {
            
            $updateSQL = "UPDATE admin_sys SET _username = ?, Phone = ? WHERE Admin_email = ?";
            $stmt = $conn->prepare($updateSQL);
            $stmt->execute([$username, $phone]);

            // หากผู้ใช้กรอกรหัสผ่านปัจจุบันและต้องการเปลี่ยนรหัสผ่าน
            if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if (!password_verify($currentPassword, $host['User_password'])) {
                     $_SESSION['error'] = '<div class="alert alert-danger">รหัสผ่านปัจจุบันไม่ถูกต้อง</div>';
                } elseif ($newPassword !== $confirmPassword) {
                     $_SESSION['error'] = '<div class="alert alert-danger">รหัสผ่านใหม่ไม่ตรงกัน</div>';
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE admin_sys SET Admin_password = ? WHERE Admin_email = ?");
                    $stmt->execute([$hashedPassword, $email]);
                    $_SESSION['error1'] = '<div class="alert alert-success">อัปเดตรหัสผ่านและข้อมูลเรียบร้อย</div>';
                }
            } else {
                if (! $_SESSION['error1']) {
                     $_SESSION['error1'] = '<div class="alert alert-success">อัปเดตข้อมูลเรียบร้อย</div>';
                }
            }
            header("Location: ../admin/profile.php");
                exit();
        }
    } catch (PDOException $e) {
         $_SESSION['error1'] = '<div class="alert alert-danger">เกิดข้อผิดพลาด: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
}