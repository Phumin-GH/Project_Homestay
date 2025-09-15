<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Host.php';
$hostHandler = new Host($conn);
$email = $_SESSION['Host_email'];
$hosts = $hostHandler->getDataHost($email);
if ($email) {
    $avatar_initial = strtoupper(substr($email, 0, 1));
} else {
    $avatar_initial = "?";
}
if (isset($_POST['host_signup'])) {
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $Id_card = trim($_POST['id_card']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);
    $result = $hostHandler->register($email, $firstname, $lastname, $Id_card, $phone, $password, $Id_card);
    if ($result === true) {
        $hostHandler->login($email, $password);
        $_SESSION['message'] = "Login เรียบร้อย";
        header("Location: ../hosts/host-dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../hosts/host-login.php?tab=signup");
        exit();
    }
}
if (isset($_POST['host_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    // ตรวจสอบว่าไม่ว่าง
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลและรหัสผ่านทั้งสอง.";
        exit();
    }
    $result = $hostHandler->login($email, $password);
    if ($result === true) {
        $_SESSION['message'] = "Login เรียบร้อย";
        header("Location: ../hosts/host-dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../hosts/host-login.php?tab=signup");
        exit();
    }
}
if (isset($_POST['save_edit'])) {
    $message = '';
    $email = $_SESSION['Host_email'];
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $currentPassword = trim($_POST['current_password']);
    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $result = $hostHandler->updateProfile($email, $firstname, $lastname, $phone, $currentPassword, $newPassword, $confirmPassword);

    if ($result === true || $result === "success") {
        $_SESSION['message'] = "แก้ไข้ข้อมูลเรียบร้อย";
        header("Location: ../hosts/profile.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../hosts/profile");
        exit();
    }
}
