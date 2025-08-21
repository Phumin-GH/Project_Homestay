<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/db_connect.php'; // ต้องแน่ใจว่า $conn คือ PDO instance


if (isset($_POST['save_signup'])) {
    $email = trim($_POST['email']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);

    // Validate inputs
    if (empty($email) || empty($firstname) || empty($lastname) || empty($phone) || empty($password) || empty($confirm_password)) {
        $_SESSION['error1'] = "กรุณากรอกข้อมูลให้ครบ.";
        header("Location: ../users/user-login.php");
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error1'] = "รูปแบบอีเมลไม่ถูกต้อง.</i>";
        header("Location: ../users/user-login.php");
    } elseif ($password !== $confirm_password) {
        $_SESSION['error1'] = "รหัสผ่านไม่ตรงกัน.";
        header("Location: ../users/user-login.php?tab=signup");
    }
    if (!isset($_SESSION['error1']) ) {
        try {
            
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM user WHERE User_email = ?");
            $checkStmt->execute([$email]);
            $count = $checkStmt->fetchColumn();

            if ($count > 0) {
                $_SESSION['error'] = "ชื่ออีเมลซ้ำกับบัญชีอื่น.";
                header("Location: ../users/user-login.php?tab=signup");
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO user (User_email, Firstname,Lastname, Phone, User_password) VALUES (?, ?, ?,?, ?)");
                $stmt->execute([$email, $firstname, $lastname, $phone, $hashed_password]);
                $_SESSION['User_email'] = $email;
                $_SESSION['message'] = "Sign upเรียบร้อย";
                
                header("Location: ../users/user-login.php?tab=signup");
                exit();
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            $_SESSION['message'] = "รหัสผ่านใหม่และยืนยันไม่ตรงกัน";
            header("Location: ../users/user-login.php?tab=signup");
            exit();
        }
    }
}
if (isset($_POST['save_login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // ตรวจสอบว่าไม่ว่าง
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "กรุณากรอกอีเมลและรหัสผ่านทั้งสอง.";
        header("Location: ../users/user-login.php?tab=login");
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM user WHERE User_email = ? ");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user){
                 if ( $user["User_Status"] != 0 ) {
                    $_SESSION['error'] = "บัญชีของคุณถูกปิดถาวร.";
                    header("Location: ../users/user-login.php?tab=login");
                }else if ( password_verify($password, $user['User_password'])) {
                    
                    $_SESSION['User_email'] = $user['User_email'];
                    $_SESSION['message'] = "Login เรียบร้อย";
                    header("Location: ../users/main-menu.php");
                    exit();
                } else {
                    $_SESSION['error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง.";
                    header("Location: ../users/user-login.php?tab=login");
                }
            }else {
            $_SESSION['error'] = "ไม่พบบัญชีผู้ใช้นี้";
            header("Location: ../users/user-login.php?tab=login");
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
if(isset($_SESSION['User_email'])){
    $email = $_SESSION['User_email'];
    $sql ="SELECT * FROM user WHERE User_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

}
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