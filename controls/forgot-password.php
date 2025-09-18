<?php
// forgot-password.php
header('Content-Type: application/json');
date_default_timezone_set("Asia/Bangkok");
require_once __DIR__ . '../model/config/db_connect.php'; // เชื่อมต่อ MySQL
require_once __DIR__ . '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (isset($_POST['User_email'])) {
    $User_email = $_POST['User_email'] ?? '';
    $stmt = $conn->prepare("SELECT User_id FROM user WHERE User_email=?");
    $stmt->execute([$User_email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo json_encode(["success" => false, "message" => "Email not found."]);
        exit;
    }
    // สร้าง token
    $token = bin2hex(random_bytes(16));
    $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));
    $stmt = $conn->prepare("SELECT COUNT(Expires_at) FROM user WHERE User_id = ?");
    $stmt->execute([$user['User_id']]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $stmt = $conn->prepare("UPDATE user SET  Expires_at = null WHERE User_id = ?");
        $stmt->execute([$user['User_id']]);
    }
    $stmt = $conn->prepare("UPDATE user SET Token =?, Expires_at = ? WHERE User_id = ?");
    $stmt->execute([$token, $expires, $user['User_id']]);
} elseif (isset($_POST['Host_email'])) {
    $Host_email = $_POST['Host_email'] ?? '';
    $stmt = $conn->prepare("SELECT Host_id FROM host WHERE Host_email=?");
    $stmt->execute([$Host_email]);
    $host = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$host) {
        echo json_encode(["success" => false, "message" => "Email not found."]);
        exit;
    }
    // สร้าง token
    $token = bin2hex(random_bytes(16));
    $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));
    $stmt = $conn->prepare("SELECT COUNT(Expires_at) FROM host WHERE Host_id = ?");
    $stmt->execute([$host['Host_id']]);
    $count = $stmt->fetchColumn();
    if ($count > 0) {
        $stmt = $conn->prepare("UPDATE host SET  Expires_at = null WHERE Host_id = ?");
        $stmt->execute([$host['Host_id']]);
    }
    $stmt = $conn->prepare("UPDATE host SET Token =?, Expires_at = ? WHERE Host_id = ?");
    $stmt->execute([$token, $expires, $host['Host_id']]);
}
$mail = new PHPMailer(true);
try {
    // ตั้งค่า SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['EMAIL'];
    $mail->Password   = $_ENV['PASSWORD']; // ใช้ App Password ไม่ใช่ password ปกติ
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // ผู้ส่ง
    $mail->setFrom($_ENV['EMAIL'], 'Homestay Management');
    // ผู้รับ
    if (isset($host)) {
        $Email = $Host_email;
    } elseif (isset($user)) {
        $Email = $User_email;
    }
    $mail->addAddress($Email, 'User');

    // เนื้อหา
    $mail->isHTML(true);
    $mail->Subject = 'Reset Password Link';
    $resetLink = "http://localhost/homestay/reset-password.php?token=$token";
    $mail->Body    = "สวัสดีครับ,<br><br>
                      กรุณาคลิกที่ลิงก์ด้านล่างเพื่อเปลี่ยนรหัสผ่านของคุณ:<br>
                      <a href='$resetLink'>$resetLink</a>";

    // ส่ง email (สำหรับทดสอบ, จริงๆ ใช้ PHPMailer หรือ SMTP)
    $mail->send();
    echo json_encode(["success" => true, "message" => "ส่งอีเมลสำเร็จ"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}"]);
}
