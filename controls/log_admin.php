<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Admin.php';
$adminHandler = new Admin($conn);

$email = $_SESSION['Admin_email'];
$admin = $adminHandler->getDataAdmin($email);
if ($email) {
    $avatar_initial = strtoupper(substr($email, 0, 1));
} else {
    $avatar_initial = "?";
}

if (isset($_POST['admin_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $result = $adminHandler->login($email, $password);
    if ($result === true) {
        $_SESSION['message'] = "Login เรียบร้อย";
        header("Location: ../admin/admin-dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../admin/admin-login.php");
        exit();
    }
}

if (isset($_POST['save_edit'])) {
    $email = $_SESSION['User_email'];
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $result = $adminHandler->updateProfile($email, $username, $phone, $currentPassword, $newPassword, $confirmPassword);
    if ($result === true || $result === "success") {
        echo   "<script>alert(แก้ไข้ข้อมูลเรียบร้อย);</script>";
        header("Location: ../admin/profile.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../admin/profile.php");
        exit();
    }
}
