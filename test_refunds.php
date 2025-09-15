<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/');
$dotenv->load();

define('OMISE_API_VERSION', '2019-05-29');
define('OMISE_PUBLIC_KEY', $_ENV['OMISE_PUBLIC_KEY']);
define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);

try {
    // 1. กำหนด Charge ID ที่ต้องการจะ Refund
    $charge_id = 'chrg_test_64v2200bgoe63lurki9'; // Charge ID ที่ต้องมีสถานะ successful

    // 2. ดึงข้อมูล Charge ต้นทางขึ้นมาก่อน
    //    ขั้นตอนนี้สำคัญมาก เพื่อให้แน่ใจว่า Charge นี้มีอยู่จริงและสามารถ Refund ได้
    echo "Retrieving Charge: " . $charge_id . "\n";
    $charge = OmiseCharge::retrieve($charge_id);

    // 3. สร้าง Refund จาก Charge object ที่ดึงมา
    //    คุณสามารถกำหนดจำนวนเงินที่ต้องการคืนได้ที่นี่
    //    ตัวอย่างนี้คือการคืนเงินเต็มจำนวน 100.00 บาท (10000 สตางค์)
    $refund_amount = 10000; 

    echo "Creating refund for amount: " . ($refund_amount / 100) . " THB\n";
    $refund = $charge->refunds()->create([
        'amount' => $refund_amount,
        'metadata' => [
            'reason' => 'User requested a refund.'
        ]
    ]);

    // 4. ตรวจสอบสถานะและแสดงผล
    if ($refund['object'] == 'refund') {
        echo "✅ Refund created successfully!\n";
        echo "Refund ID: " . $refund['id'] . "\n";
        echo "Refunded Amount: " . ($refund['amount'] / 100) . " " . strtoupper($refund['currency']) . "\n";
        echo "Transaction ID: " . $refund['transaction'] . "\n";
    } else {
        echo "❌ Failed to create refund.\n";
    }

} catch (Exception $e) {
    // แสดงข้อความ Error ที่ได้รับจาก Omise API
    echo "Error: " . $e->getMessage() . "\n";
}