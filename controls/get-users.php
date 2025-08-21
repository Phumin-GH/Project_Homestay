<?php
// get_users.php
include __DIR__ . '/../config/db_connect.php';



$stmt = $conn->prepare("SELECT * FROM User");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT * FROM Host");
$stmt->execute();
$hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>