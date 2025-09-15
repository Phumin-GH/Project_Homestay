<?php
ob_start();
// header('Content-Type: application/json');
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__.'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/'); // path ไป root ของโปรเจกต์
$dotenv->load();
define('OMISE_API_VERSION', '2019-05-29');
define('OMISE_PUBLIC_KEY',$_ENV['OMISE_PUBLIC_KEY']);
define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);

    // $booking_id = $_POST['booking_id'] ?? '';
    $name = 'Phumin Duangchanta';
    $number = '4242424242424242';
    $exMonth = '12';
    $exYear = '2025';
    $cvv = '123';
    $amount =100000;
try {
    $token = OmiseToken::create([
        'card' => [
            'name' => $name,
            'number' => $number,
            'expiration_month' => $exMonth,
            'expiration_year' => $exYear,
            'security_code' => $cvv
        ]
    ]);

    $charge = OmiseCharge::create([
        'amount' => $amount,
        'currency' => 'thb',
        'card' => $token['id'],
        'description' => 'Test Payment via '
    ]);
echo "Charge ID: " . $charge['id'] . "<br>";
echo "Status: " . $charge['status'] . "<br>";
echo "Authorized? " . ($charge['authorized'] ? 'Yes' : 'No') . "<br>";
echo "Paid? " . ($charge['paid'] ? 'Yes' : 'No') . "<br>";
    if ($charge['status'] === 'successful') {
        // Save the booking information to the database
        // $sql = "UPDATE booking SET Payment_status = ?, Charge_id = ? WHERE Booking_id = ?";
        // $stmt = $conn->prepare($sql);
        // $stmt->execute([  $charge['status'], $charge['id'],$booking_id]);
        echo  "✅ Payment successful! " . $charge['status'];
    } else {
        echo  "❌ Payment failed. Status: " . $charge['status'];
    }

} catch (Exception $e) {
    echo  "Error: " . $e->getMessage();
}