<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/db_connect.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Mpdf\Mpdf;

session_start();

if (isset($_SESSION['email_data'])) {
    try {

        $customerName = $_SESSION['email_data']['user_firstname'] . " " . $_SESSION['email_data']['user_lastname'];
        $customerEmail = $_SESSION['email_data']['user_email'];
        $bookingId =  $_SESSION['email_data']['booking_id'];
        $checkInDate = $_SESSION['email_data']['checkIn'];
        $checkOutDate = $_SESSION['email_data']['checkOut'];
        $proName = $_SESSION['email_data']['property_name'];
        $proProvince = $_SESSION['email_data']['property_pro'];
        $proDis = $_SESSION['email_data']['property_dis'];
        $proSub = $_SESSION['email_data']['property_sub'];
        $roomNum = $_SESSION['email_data']['room_num'];
        $roomCap = $_SESSION['email_data']['room_cap'];
        $guests = $_SESSION['email_data']['guests'];
        $hostName = $_SESSION['email_data']['host_firstname'];
        $hostPhone = $_SESSION['email_data']['host_phone'];
        $total_price = $_SESSION['email_data']['total_price'];
        $bookingDate = $_SESSION['email_data']['bookingDate'];

        $stmt = $conn->prepare("SELECT Charge_id FROM Booking WHERE Booking_id = ? ");
        $stmt->execute([$bookingId]);
        $chargeId = $stmt->fetchColumn();

        // --- ตั้งค่า mPDF พร้อมฟอนต์ไทย THSarabun ---
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'fontDir' => array_merge($fontDirs, [__DIR__ . '/../font']),
            'fontdata' => $fontData + [
                'thsarabun' => [
                    'R' => 'THSarabun.ttf',
                    'B' => 'THSarabun_Bold.ttf',
                ]
            ],
            'default_font' => 'thsarabun',
            'format' => 'A5',   // ขนาดกระดาษเหมาะสำหรับใบเสร็จ
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10
        ]);



        // --- สร้าง HTML ใบเสร็จแบบ Minimal ---
        $html = "
<style>
    body {
        font-family: thsarabun, sans-serif;
        font-size: 14pt;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .header {
        text-align: center;
        margin-bottom: 5px;
    }

    .hotel-name {
        font-size: 22pt;
        font-weight: bold;
        color: #007bff;
        margin: 5px 0 5px 0;
    }

    .title {
        font-size: 24pt;
        margin: 5px 0 5px 0;
        font-weight: bold;
    }

    .subtitle {
        font-size: 18pt;
        margin: 2.5px 0 10px 0;
        color: #555;
    }

    .date {
        font-size: 14pt;
        color: #555;
    }
    .address{
    	font-size: 16pt;
        margin: 0 0 5px 0;
    }
    .host{
    font-size: 14pt;
    margin: 0 0 20px 0;
    
    }

    .section-title {
        font-size: 16pt;
        margin: 10px 0 0px 0;
        font-weight: bold;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 5px 20px;
    }

    td,
    th {
        padding: 6px;
        vertical-align: top;
    }

    .info-table td:first-child {
        width: 35%;
        font-weight: bold;
    }

    .footer {
        text-align: center;
        margin-top: 20px;
        font-size: 12pt;
        color: #777;
    }

    .border {
        border-bottom: 1px solid #ccc;
        margin: 2px 0;
    }
    </style>

    <div class='header'>
        <div class='title'>Homestay Management</div>
        <div class='subtitle'>Booking Confirmation</div>
        <div class='hotel-name'>{$proName}</div>
        <div class='address'>{$proSub}, {$proDis}, {$proProvince}</div>
        <div class='host'>เจ้าของ: {$hostName} | เบอร์โทร: {$hostPhone}</div>
    </div>

    <table class='info-table'>
        <tr>
            <td class='section-title'>รายละเอียดการจอง</td>
            <td class='date'>วันที่  {$bookingDate}</td>
        </tr>
    </table>
    <div class='border'></div>

    <table class='info-table'>
    
        <tr>
            <td>รหัสการจอง:</td>
            <td>{$chargeId}</td>
        </tr>
        <tr>
            <td>ชื่อผู้จอง:</td>
            <td>{$customerName}</td>
        </tr>
        <tr>
            <td>Check-in:</td>
            <td>{$checkInDate}</td>
        </tr>
        <tr>
            <td>Check-out:</td>
            <td>{$checkOutDate}</td>
        </tr>
    </table>
   
    <table class='info-table'>
        <tr>
            <td class='section-title'>ห้องที่จอง & ผู้เข้าพัก</td>
        </tr>
    </table>
 <div class='border'></div>
 <table class='info-table'>
        <tr>
            <td>ห้องหมายเลข:</td>
            <td> {$roomNum}</td>
        </tr>
        <tr>
            <td>ผู้เข้าพัก:</td>
            <td>{$guests} ท่าน</td>
        </tr>
        <tr>
            <td>สิ่งอำนวยความสะดวก:</td>
            <td>{$roomCap}</td>
        </tr>
        
</table>
<div class='border'></div>
<table class='info-table'>
        <tr>
            <td class='section-title'>Total:</td>
            <td><strong>" . number_format($total_price) . " บาท</td>
        </tr>
</table>
    <div class='footer'>
        This is a computer-generated receipt.<br>
        No signature is required.
    </div>";
        $mpdf->WriteHTML($html);
        // --- เซฟ PDF ชั่วคราว ---
        $pdfFile = "../PDF/receipt_" . $chargeId . ".pdf";
        $mpdf->Output($pdfFile, \Mpdf\Output\Destination::FILE);
        // --- ส่ง Email ด้วย PHPMailer ---
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['EMAIL'];
        $mail->Password   = $_ENV['PASSWORD'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($_ENV['EMAIL'], 'Homestay Management');
        $mail->addAddress($customerEmail, 'ลูกค้า');

        $mail->addAttachment($pdfFile);
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Subject = 'ใบเสร็จชำระเงิน ' . $hostName;
        $mail->Body    = 'สวัสดีครับ แนบใบเสร็จชำระเงินมาให้ <b>เช็คได้เลยครับ</b>';

        $mail->send();
        $stmt = $conn->prepare("UPDATE booking SET Email_status = 'sended' WHERE Booking_id = ?");
        $stmt->execute([$bookingId]);

        echo "<script>
        alert('ส่งใบเสร็จ PDF ภาษาไทยสำเร็จแล้ว!');
        window.location.href = 'main-menu.php?';
         </script>";
    } catch (Exception $e) {
        $stmt = $conn->prepare("UPDATE booking SET Email_status = 'failed' WHERE Booking_id = ?");
        $stmt->execute([$bookingId]);
        echo "<script>alert('ส่งใบเสร็จ PDF ภาษาไทยไม่สำเร็จ: " . $e->getMessage() . "'); </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="website icon" type="png" href="/images/logo.png">
    <title>Send Gmail</title>
</head>

<body>

</body>

</html>