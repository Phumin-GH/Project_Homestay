<?php
// 1. เรียกใช้งาน Autoloader ของ Composer
// เพื่อให้โปรเจกต์รู้จักไลบรารีที่เราติดตั้งไป
require_once __DIR__ . '/vendor/autoload.php';

// 2. โหลดตัวแปรจากไฟล์ .env
// บรรทัดนี้สำคัญมาก! เป็นการบอกให้ phpdotenv อ่านไฟล์ .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 3. ตั้งค่า Key ให้กับ Omise
// เราจะใช้ค่าที่อ่านมาจาก $_ENV ซึ่งตอนนี้มีข้อมูลจากไฟล์ .env แล้ว
define('OMISE_PUBLIC_KEY', $_ENV['OMISE_PUBLIC_KEY']);
define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);

// --- ส่วนของการสร้าง Charge (การเรียกเก็บเงิน) ---

try {
    // 4. สร้าง Charge สำหรับ PromptPay
    $charge = OmiseCharge::create([
        'amount'      => 50050, // ยอดเงิน 500.50 บาท (ต้องระบุเป็นหน่วยสตางค์)
        'currency'    => 'thb',
        'source'      => ['type' => 'promptpay'], // ระบุแหล่งที่มาของเงินเป็น "promptpay"
        'description' => 'Order ID: 12345', // คำอธิบายรายการ (ไม่บังคับ)
        'return_uri'  => 'https://www.yourwebsite.com/payment_complete' // ลิงก์เมื่อจ่ายเงินสำเร็จ
    ]);
    var_dump($charge);

    // 5. แสดงผลลัพธ์และ QR Code
    echo "<h1>สแกนเพื่อชำระเงิน</h1>";
    echo "<p>สถานะ: " . $charge['status'] . "</p>";

    // ตรวจสอบว่ามี QR Code ส่งกลับมาหรือไม่
    if (isset($charge['source']['scannable_code']['image']['download_uri'])) {
        $qrCodeUrl = $charge['source']['scannable_code']['image']['download_uri'];
        echo '<img src="' . $qrCodeUrl . '" alt="PromptPay QR Code" width="300">';
    } else {
        echo "ไม่สามารถสร้าง QR Code ได้";
    }

    // (แนะนำ) สามารถแสดงข้อมูลทั้งหมดเพื่อตรวจสอบได้
    // echo '<pre>';
    // print_r($charge);
    // echo '</pre>';

} catch (Exception $e) {
    // จัดการกับข้อผิดพลาดที่อาจเกิดขึ้น
    echo "เกิดข้อผิดพลาด: " . $e->getMessage();
}
