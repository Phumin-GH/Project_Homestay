<?php
session_start();
if (!isset($_SESSION["User_email"])) {
    header("Location: ../index.php");
    exit();
}
include '../config/db_connect.php';
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['house_id'])) {
    $property_id = $_POST['house_id'];
    
    $stmt = $conn->prepare("SELECT p.*, h.Host_firstname, h.Host_lastname FROM property p INNER JOIN host h ON p.Host_id = h.Host_id WHERE p.Property_id = ?");
$stmt->execute([$property_id]);
$house = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$house['Property_id']) {
    echo "ไม่พบข้อมูลบ้านพัก!!!!";
    exit();
}

}
// guest เห็น property ที่อนุมัติแล้ว
?>