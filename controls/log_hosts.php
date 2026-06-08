<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Host.php';

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    $_SESSION['error'] = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
    header("Location: ../views/hosts/host-login.php?tab=signup");
    exit();
}

$hostHandler = new Host($conn);
if (isset($_SESSION['Host_email'])) {
    $email = $_SESSION['Host_email'];
    $hosts = $hostHandler->getDataHost($email);
    if ($email) {
        $avatar_initial = strtoupper(substr($email, 0, 1));
    } else {
        $avatar_initial = "?";
    }
}
if (isset($_POST['host_signup'])) {
    try {
        // Debug: ดูข้อมูลที่ส่งมา
        error_log("POST data: " . print_r($_POST, true));

        // รับและทำความสะอาดข้อมูล
        $email = trim($_POST['email'] ?? '');
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $Id_card = trim($_POST['id_card'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm-password'] ?? '');

        // ตรวจสอบข้อมูลพื้นฐาน
        if (empty($email) || empty($firstname) || empty($lastname) || empty($Id_card) || empty($phone) || empty($password) || empty($confirm_password)) {
            $_SESSION['error'] = "กรุณากรอกข้อมูลให้ครบถ้วน";
            header("Location: ../views/hosts/host-login.php?tab=signup");
            exit();
        }

        // ตรวจสอบรูปแบบอีเมล
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = "รูปแบบอีเมลไม่ถูกต้อง";
            header("Location: ../views/hosts/host-login.php?tab=signup");
            exit();
        }

        // ตรวจสอบรหัสผ่าน
        if ($password !== $confirm_password) {
            $_SESSION['error'] = "รหัสผ่านไม่ตรงกัน";
            header("Location: ../views/hosts/host-login.php?tab=signup");
            exit();
        }

        // ลงทะเบียน
        $result = $hostHandler->register($email, $firstname, $lastname, $Id_card, $phone, $password, $confirm_password);

        // Debug: บันทึกผลลัพธ์
        error_log("Host registration result: " . print_r($result, true));

        if ($result === true) {
            // ลองเข้าสู่ระบบหลังลงทะเบียนสำเร็จ
            $loginResult = $hostHandler->login($email, $password);

            if ($loginResult === true) {
                $_SESSION['message'] = "ลงทะเบียนและเข้าสู่ระบบเรียบร้อย";
                header("Location: ../views/hosts/host-dashboard.php");
                exit();
            } else {
                $_SESSION['message'] = "ลงทะเบียนสำเร็จ กรุณาเข้าสู่ระบบ";
                header("Location: ../views/hosts/host-login.php?tab=login");
                exit();
            }
        } else {
            // จัดการ error ที่ return มาจาก register method
            if (is_array($result)) {
                // ถ้าเป็น array ของ error messages
                $errorMessages = [];
                if (isset($result['pwd']))
                    $errorMessages[] = $result['pwd'];
                if (isset($result['con_pwd']))
                    $errorMessages[] = $result['con_pwd'];
                if (isset($result['email']))
                    $errorMessages[] = $result['email'];
                if (isset($result['general']))
                    $errorMessages[] = $result['general'];

                $_SESSION['error'] = !empty($errorMessages) ? implode(', ', $errorMessages) : "เกิดข้อผิดพลาดในการลงทะเบียน";
            } else {
                // ถ้าเป็น string
                $_SESSION['error'] = $result;
            }

            header("Location: ../views/hosts/host-login.php?tab=signup");
            exit();
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดในระบบ: " . $e->getMessage();
        header("Location: ../views/hosts/host-login.php?tab=signup");
        exit();
    }
}
if (isset($_POST['host_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่าไม่ว่าง
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลและรหัสผ่านทั้งสอง";
        header("Location: ../views/hosts/host-login.php?tab=login");
        exit();
    }

    $result = $hostHandler->login($email, $password);
    if ($result === true) {
        $_SESSION['message'] = "เข้าสู่ระบบเรียบร้อย";
        header("Location: ../views/hosts/host-dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../views/hosts/host-login.php?tab=login");
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
        $_SESSION['message'] = "แก้ไขข้อมูลเรียบร้อย";
        header("Location: ../views/hosts/profile.php");
        exit();
    } else {
        $_SESSION['error'] = $result;
        header("Location: ../views/hosts/profile");
        exit();
    }
}