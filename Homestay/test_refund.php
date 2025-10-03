<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/'); // path ไป root ของโปรเจกต์
$dotenv->load();
define('OMISE_API_VERSION', '2019-05-29');
define('OMISE_PUBLIC_KEY',$_ENV['OMISE_PUBLIC_KEY']);
define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);

try {
    // สร้าง Charge ด้วย token ทดสอบบัตรเครดิต (Visa)
    $total_price = 10000 * 0.8;
    $charge_id = 'chrg_test_64v2200bgoe63lurki9'; // ตัวอย่าง Charge ID ที่ต้องการทำ Refund
    $charge = OmiseRefund::create([
        'charge' => $charge_id,
        'amount' => $total_price,
        
    ]);
    echo "Charge created with ID: " . $charge['id'] . "\n";

    // ทำ Refund คืนเต็มจำนวน
    // $refund = $charge->refunds()->create([
    //     'amount' => 10000, // 100.00 บาท
    // ]);
    // echo "Refunded amount: " . ($refund['amount'] / 100) . " บาท\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}