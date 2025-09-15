<?php
ob_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // path ไป root ของโปรเจกต์
$dotenv->load();
define('OMISE_API_VERSION', '2019-05-29');
define('OMISE_PUBLIC_KEY', $_ENV['OMISE_PUBLIC_KEY']);
define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);

$booking_id = $_POST['booking_id'] ?? '';
$name = $_POST['Username'] ?? '';
$number = $_POST['number_card'] ?? '';
$exMonth = $_POST['expMonth'] ?? '';
$exYear = $_POST['expYear'] ?? '';
$cvv = $_POST['cvv'] ?? '';
$amount = $_POST['amount'] ?? '';
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


    if ($charge['status'] === 'successful') {
        // Save the booking information to the database
        $number_card = null;
        if ($number == '4242424242424242') {
            $number_card = 'Visa';
        } elseif ($number == '5555555555554444') {
            $number_card = 'MasterCard';
        }
        $sql = "UPDATE booking SET Payment_status = ?, Payment_gateway = ?, Booking_status = ?, Charge_id = ? WHERE Booking_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['paid', $number_card, 'successful', $charge['id'], $booking_id]);
        echo json_encode(["success" => true, "message" => "✅ Payment successful! " . $charge['status'], $charge['id']]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ Payment failed. Status: " . $charge['status']]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
