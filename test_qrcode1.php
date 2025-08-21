<?php
require_once __DIR__ . '/vendor/autoload.php';

define('OMISE_PUBLIC_KEY', 'pkey_test_64nbbhnxh0371dz2kzi');
define('OMISE_SECRET_KEY', 'skey_test_64nbbhodcchurub65uw');
$charges = OmiseCharge::retrieve();
foreach ($charges['data'] as $ch) {
    echo "ID: " . $ch['id'] . "<br>";
    echo "Amount: " . ($ch['amount'] / 100) . " บาท<br>";
    echo "Status: " . $ch['status'] . "<br>";
    $timestamp = strtotime($ch['created_at']);
    echo "Created: " . date('Y-m-d H:i:s', $timestamp) . "<hr>";

    
}
print_r($ch);