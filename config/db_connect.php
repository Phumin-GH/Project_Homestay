<?php
$host = "localhost";
$dbname = "homestay_db";
$user = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // ตั้งค่าให้ PDO แจ้งเตือนข้อผิดพลาดแบบ exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connection successful"; // ใช้ทดสอบการเชื่อมต่อ
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}