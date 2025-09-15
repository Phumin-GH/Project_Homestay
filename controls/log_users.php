<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. เรียกใช้ไฟล์ที่จำเป็น
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/User.php';

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
    // รับค่าและตรวจสอบข้อมูลเบื้องต้น (เหมือนเดิม)
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);

    //เรียกใช้ Method register() จาก Object
    $result = $userHandler->register($email, $firstname, $lastname, $phone, $password);
    if ($result === "Sign in เรียบร้อย.") {
        // ลงทะเบียนสำเร็จ, ทำการล็อกอินเลย
        $userHandler->login($email, $password);
        $_SESSION['message'] = "Sign up เรียบร้อย.";
        header("Location: ../users/main-menu.php"); // ไปยังหน้าหลัก
        exit();
    } else {
        // ถ้าไม่สำเร็จ ให้แสดงข้อผิดพลาด
        $_SESSION['error'] = $result;
        header("Location: ../users/user-login.php?tab=signup");
        exit();
    }
}
// --- จัดการการล็อกอิน ---
if (isset($_POST['save_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 3. เรียกใช้ Method login() จาก Object
    $result = $userHandler->login($email, $password);

    if ($result === true) {
        $_SESSION['message'] = "Login เรียบร้อย.";
        header("Location: ../users/main-menu.php"); // ไปยังหน้าหลัก
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../users/user-login.php");
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
        echo   "<script>alert(แก้ไข้ข้อมูลเรียบร้อย);</script>";
        header("Location: ../users/profile.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../users/profile.php");
        exit();
    }
}

// (ส่วนของการแก้ไขโปรไฟล์ก็จะใช้หลักการเดียวกัน)