<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // path ไป vendor/autoload.php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // path ไป root ของโปรเจกต์
$dotenv->load();

$host = $_ENV['DB_Host'];
$db   = $_ENV['DB_Name'];
$user = $_ENV['DB_User'];
$pass = $_ENV['DB_Password'];
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
