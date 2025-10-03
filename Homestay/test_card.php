<?php
require_once dirname(__FILE__).'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // path ไป root ของโปรเจกต์
$dotenv->load();
define('OMISE_API_VERSION', '2019-05-29');
define('OMISE_PUBLIC_KEY',$_ENV['OMISE_PUBLIC_KEY']);
define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);

try {
    $token = OmiseToken::create([
        'card' => [
            'name' => $_POST['name'],
            'number' => $_POST['number'],
            'expiration_month' => (int)$_POST['expiration_month'],
            'expiration_year' => (int)$_POST['expiration_year'],
            'security_code' => $_POST['security_code']
        ]
    ]);

    $charge = OmiseCharge::create([
        'amount' => (int)$_POST['total_price'],   // 100.00 บาท
        'currency' => 'thb',
        'card' => $token['id'],
        'description' => 'Test Payment via AJAX'
    ]);

    if ($charge['status'] === 'successful') {
        echo "✅ Payment successful! Charge ID: " . $charge['id'];
    } else {
        echo "❌ Payment failed. Status: " . $charge['status'];
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}