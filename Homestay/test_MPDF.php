<?php
require_once __DIR__ . '/vendor/autoload.php'; // autoload จาก composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf;

try {
    // 1) สร้าง PDF ด้วย mPDF
    $mpdf = new Mpdf([
        'default_font' => 'sarabun' // ใช้ฟอนต์ไทย (sarabun ต้องลงก่อน)
    ]);

    // เนื้อหา PDF
    $html = '
    <h2 style="text-align:center;">ทดสอบสร้าง PDF ภาษาไทย</h2>
    <p>สวัสดีครับ นี่คือเอกสาร PDF ที่สร้างด้วย <b>mPDF</b></p>
    <p>วันที่: ' . date("Y-m-d H:i:s") . '</p>
    ';

    $mpdf->WriteHTML($html);

    // เซฟไฟล์ PDF ลงชั่วคราว
    $pdfFile = __DIR__ . "/output.pdf";
    $mpdf->Output($pdfFile, \Mpdf\Output\Destination::FILE);

    // 2) ส่ง Email ด้วย PHPMailer
    $mail = new PHPMailer(true);

    // ตั้งค่า SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_email@gmail.com'; // ใส่อีเมลของคุณ
    $mail->Password   = 'your_app_password';    // ใช้ App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587;

    // ผู้ส่ง
    $mail->setFrom('your_email@gmail.com', 'PDF Sender');
    // ผู้รับ
    $mail->addAddress('receiver@example.com', 'คุณผู้รับ');

    // แนบไฟล์ PDF
    $mail->addAttachment($pdfFile);

    // เนื้อหา Email
    $mail->isHTML(true);
    $mail->Subject = 'ส่ง PDF ภาษาไทย ด้วย mPDF + PHPMailer';
    $mail->Body    = 'สวัสดีครับ แนบไฟล์ PDF มาด้วย <b>เช็คเลยครับ</b>';

    $mail->send();
    echo "📧 ส่งอีเมลพร้อมไฟล์ PDF สำเร็จแล้ว!";
} catch (Exception $e) {
    echo "❌ ส่งไม่สำเร็จ: {$mail->ErrorInfo}";
}