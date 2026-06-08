<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. เรียกใช้ไฟล์ที่จำเป็น
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/User.php';

// 2. สร้าง Object จาก Class User
$userHandler = new User($conn);
$email = $_SESSION['User_email'];
//ดึงข้อมูลUsers
$users = $userHandler->getDataUser($email);
//โปรไฟล์ Avatar
if ($email) {
    $avatar_initial = strtoupper(substr($email, 0, 1));
} else {
    $avatar_initial = "?";
}
// --- จัดการการลงทะเบียน ---
if (isset($_POST['save_signup'])) {
    try {
        // รับและทำความสะอาดข้อมูล
        $email = trim($_POST['email']);
        $firstname = trim($_POST['firstname']);
        $lastname = trim($_POST['lastname']);
        $phone = trim($_POST['phone']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm-password']);

        // ตรวจสอบข้อมูลพื้นฐาน
        if (empty($email) || empty($firstname) || empty($lastname) || empty($phone) || empty($password) || empty($confirm_password)) {
            $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
            header("Location: ../views/users/user-login.php?tab=signup");
            exit();
        }

        // ตรวจสอบรูปแบบอีเมล
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "รูปแบบอีเมลไม่ถูกต้อง";
            header("Location: ../views/users/user-login.php?tab=signup");
            exit();
        }

        // ตรวจสอบรหัสผ่าน
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "รหัสผ่านไม่ตรงกัน";
            header("Location: ../views/users/user-login.php?tab=signup");
            exit();
        }

        // เรียกใช้ Method register() จาก Object
        $result = $userHandler->register($email, $firstname, $lastname, $phone, $password, $confirm_password);

        if ($result === true) {
            // ลงทะเบียนสำเร็จ, ทำการล็อกอินเลย
            $loginResult = $userHandler->login($email, $password);

            if ($loginResult === true) {
                $_SESSION['message'] = "ลงทะเบียนและเข้าสู่ระบบเรียบร้อย";
                header("Location: ../views/users/main-menu.php");
                exit();
            } else {
                $_SESSION['message'] = "ลงทะเบียนสำเร็จ กรุณาเข้าสู่ระบบ";
                header("Location: ../views/users/user-login.php?tab=login");
                exit();
            }
        } else {
            // ถ้าไม่สำเร็จ ให้แสดงข้อผิดพลาด
            $_SESSION['error'] = $result;
            header("Location: ../views/users/user-login.php?tab=signup");
            exit();
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในระบบ: " . $e->getMessage();
        header("Location: ../views/users/user-login.php?tab=signup");
        exit();
    }
}
// --- จัดการการล็อกอิน ---
if (isset($_POST['save_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่าไม่ว่าง
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลและรหัสผ่านทั้งสอง";
        header("Location: ../views/users/user-login.php?tab=login");
        exit();
    }

    $result = $userHandler->login($email, $password);
    if ($result === true) {
        $_SESSION['message'] = "เข้าสู่ระบบเรียบร้อย";
        header("Location: ../views/users/main-menu.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../views/users/user-login.php?tab=login");
        exit();
    }
}
if (isset($_POST['save_edit'])) {
    $message = '';
    $email = $_SESSION['User_email'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $result = $userHandler->updateProfile($email, $firstname, $lastname, $phone, $currentPassword, $newPassword, $confirmPassword);

    if ($result === true || $result === "success") {
        $_SESSION['message'] = "แก้ไขข้อมูลเรียบร้อย";
        header("Location: ../views/users/profile.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../views/users/profile.php");
        exit();
    }
}