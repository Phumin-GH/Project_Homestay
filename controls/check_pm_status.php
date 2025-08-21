<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');
define('OMISE_PUBLIC_KEY', 'pkey_test_64nbbhnxh0371dz2kzi');
define('OMISE_SECRET_KEY', 'skey_test_64nbbhodcchurub65uw');

$charge_id = $_GET['charge_id'] ?? '';

if (!$charge_id) {
    echo json_encode(['error' => 'No charge_id provided']);
    exit;
}

try {
    $charge = OmiseCharge::retrieve($charge_id);
    echo json_encode([
        'status' => $charge['status'],
        'paid' => $charge['paid'],
        'expired' => $charge['expired'] ?? false,
        'authorized' => $charge['authorized'] ?? false,
    ]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}