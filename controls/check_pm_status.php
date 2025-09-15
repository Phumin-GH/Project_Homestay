<?php
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

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
