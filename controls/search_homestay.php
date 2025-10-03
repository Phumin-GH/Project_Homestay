<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Property.php';
$searchHandler = new Property($conn);
try {
    $searchTerm = $_POST['search_query'] ?? '';
    $products = $searchHandler->search_house($searchTerm);
    echo json_encode($products, JSON_UNESCAPED_UNICODE);
    exit();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
    exit();
}
