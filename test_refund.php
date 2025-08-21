<?php
require_once __DIR__ . '/vendor/autoload.php';

define('OMISE_PUBLIC_KEY', 'pkey_test_64nbbhnxh0371dz2kzi');
define('OMISE_SECRET_KEY', 'skey_test_64nbbhodcchurub65uw');

try {
    // สร้าง Charge ด้วย token ทดสอบบัตรเครดิต (Visa)
    $charge = OmiseCharge::create([
        'amount' => 10000, // 100.00 บาท (หน่วยสตางค์)
        'currency' => 'thb',
        'card' => 'tokn_test_visa_4242',
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
?>