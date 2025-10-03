<?php
// forgot-password.php
header('Content-Type: application/json');
date_default_timezone_set("Asia/Bangkok");
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../model/dao/Forgot_Password.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$forgotHandler = new Forgot_Password($conn);

$mail = new PHPMailer(true);
try {
    // ตั้งค่า SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['EMAIL'];
    $mail->Password   = $_ENV['PASSWORD'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // ผู้ส่ง
    $mail->setFrom($_ENV['EMAIL'], 'Homestay Management');
    // ผู้รับ
    if (isset($_POST['User_email'])) {
        $Email = $_POST['User_email'] ?? '';
        $token = $forgotHandler->forgot_pwd_User($Email);
        if (is_string($token)) {
            echo json_encode(["success" => false, "message" => $token]);
            exit;
        }
    } elseif (isset($_POST['Host_email'])) {
        $Email = $_POST['Host_email'] ?? '';
        $token = $forgotHandler->forgot_pwd_Host($Email);
        if (is_string($token)) {
            echo json_encode(["success" => false, "message" => $token]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "Pls provide email"]);
        exit;
    }
    $mail->addAddress($Email, 'User');
    $mail->isHTML(true);
    $mail->Subject = 'Reset Password Link';
    $resetLink = "http://localhost/homestay/views/reset-password.php?token=$token";
    $mail->Body    = "สวัสดีครับ,<br><br>
                      กรุณาคลิกที่ลิงก์ด้านล่างเพื่อเปลี่ยนรหัสผ่านของคุณ:<br>
                      <a href='$resetLink'>$resetLink</a>";

    // ส่ง email (สำหรับทดสอบ, จริงๆ ใช้ PHPMailer หรือ SMTP)
    $mail->send();
    echo json_encode(["success" => true, "message" => "ส่งอีเมลสำเร็จ"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}"]);
}