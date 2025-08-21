<?php
if(isset($_SESSION['Host_email'])){
    $email = $_SESSION["Host_email"];
// ดึงข้อมูลทั้งหมดของ host
$stmt = $conn->prepare("
    SELECT Host_firstname
    FROM host 
    WHERE Host_email = ?
");
$stmt->execute([$email]);
// ดึงแถวเดียวสำหรับ avatar และแสดงข้อมูล
$host = $stmt->fetch(PDO::FETCH_ASSOC);
if ($host) {
    $avatar_initial = strtoupper(substr($host['Host_firstname'], 0, 1));
} else {
    $avatar_initial = "?"; // fallback
}
}
if(isset($_SESSION['User_email'])){
    $email = $_SESSION["User_email"];
// ดึงข้อมูลทั้งหมดของ host
$stmt = $conn->prepare("
    SELECT Firstname
    FROM user
    WHERE User_email = ?
");
$stmt->execute([$email]);
// ดึงแถวเดียวสำหรับ avatar และแสดงข้อมูล
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user) {
    $avatar_initial = strtoupper(substr($user['Firstname'], 0, 1));
} else {
    $avatar_initial = "?"; // fallback
}
}