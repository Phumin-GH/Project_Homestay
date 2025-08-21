<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/db_connect.php'; 



if (isset($_POST['host_signup'])) {
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $Id_card = trim($_POST['id_card']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);

    // Validate inputs
    if (empty($email) || empty($Id_card)|| empty($firstname) || empty($lastname) ||empty($phone) || empty($password) || empty($confirm_password)) {
        $_SESSION['error1'] = "กรุณากรอกข้อมูลให้ครบ.";
        header("Location: ../hosts/host-login.php");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error1'] = "รูปแบบอีเมลไม่ถูกต้อง.</i>";
        header("Location: ../hosts/host-login.php");
    } elseif ($password !== $confirm_password) {
        $_SESSION['error1'] = "รหัสผ่านไม่ตรงกัน.";
        header("Location: ../hosts/host-login.php?tab=signup");
    }

    if (!isset($_SESSION['error1']) ) {
        try {
            
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM host WHERE Host_email = ?");
            $checkStmt->execute([$email]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                $_SESSION['error1'] = "ชื่ออีเมลซ้ำกับบัญชีอื่น.";
                header("Location: ../hosts/host-login.php?tab=signup");
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO user (Host_email, Host_firstname,Host_lastname, Host_phone, Host_password) VALUES (?, ?, ?,?, ?)");
                $stmt->execute([$email, $firstname, $lastname, $phone, $hashed_password]);
                $_SESSION['Host_email'] = $email;
                $_SESSION['message1'] = "Sign upเรียบร้อย";
                
                header("Location: ../hosts/host-login.php?tab=signup");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error1'] = "Database error: " . $e->getMessage();
            $_SESSION['message1'] = "รหัสผ่านใหม่และยืนยันไม่ตรงกัน";
            header("Location: ../hosts/host-login.php?tab=signup");
            exit();
        }
    }
}
if (isset($_POST['host_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่าไม่ว่าง
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลและรหัสผ่านทั้งสอง.";
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ? ");
            $stmt->execute([$email]);
            $host = $stmt->fetch(PDO::FETCH_ASSOC);
            if($host){
                 if ( $host["Host_Status"] != 0 and $host["Host_Status"] != 1) {
                    $_SESSION['error'] = "บัญชีของคุณถูกปิดถาวร.";
                    header("Location: ../hosts/host-login.php?tab=login");
                }else if ( password_verify($password, $host['Host_password'])) {
                    
                    $_SESSION['Host_email'] = $host['Host_email'];
                    $_SESSION['message'] = "Login เรียบร้อย";
                    header("Location: ../hosts/host-dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง.";
                    header("Location: ../hosts/host-login.php?tab=login");
                }
            }else {
            $_SESSION['error'] = "ไม่พบบัญชีผู้ใช้นี้";
            header("Location: ../hosts/host-login.php?tab=login");
            exit();
        }
            
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            $_SESSION['message'] = "Login ไม่ได้";
            header("Location: ../hosts/host-login.php?tab=login");
            exit();
        }
    }
}

if (empty($_SESSION['Host_email'])) {
    header("Location: host-login.php");
    exit();
}
if(isset($_SESSION['Host_email'])){
    $email = $_SESSION['Host_email'];
    $sql ="SELECT * FROM host WHERE Host_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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