<?php

include __DIR__ . '/../config/db_connect.php';


    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['house_id'])) {
        $property_id = $_POST['house_id'];
        
        $stmt = $conn->prepare("SELECT p.*, h.Host_firstname, h.Host_lastname FROM property p INNER JOIN host h ON p.Host_id = h.Host_id WHERE p.Property_id = ?");
        $stmt->execute([$property_id]);
        $house = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$house) {
            echo "ไม่พบข้อมูลบ้านพัก!!!!";
            exit();
        }
    }

    // guest เห็น property ที่อนุมัติแล้ว
    $stmt = $conn->prepare("SELECT * FROM Property INNER JOIN Host on Property.Host_id = Host.Host_id WHERE Property.Property_status = 1");
    $stmt->execute();
    $homestay = $stmt->fetchAll(PDO::FETCH_ASSOC);

// if (!isset($_GET['id'])) {
//     echo "ไม่พบรหัสบ้านพัก";
//     exit();
// }



// $property_id = $_GET['id'];
// $stmt = $conn->prepare("SELECT p.*, h.Host_firstname, h.Host_lastname FROM property p INNER JOIN host h ON p.Host_id = h.Host_id WHERE p.Property_id = ?");
// $stmt->execute([$property_id]);
// $house = $stmt->fetch(PDO::FETCH_ASSOC);
// if (!$house) {
//     echo "ไม่พบข้อมูลบ้านพัก55";
//     exit();
// }
//     // // Get rooms
$stmt = $conn->prepare("SELECT * FROM room WHERE Property_id = ?");
$stmt->execute([$property_id]);
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

// // Get bookings for calendar (assume table 'booking' with Room_id, checkin_date, checkout_date)
// $stmt = $conn->prepare("SELECT Room_id, checkin_date, checkout_date FROM booking WHERE Property_id = ?");
// $stmt->execute([$property_id]);
// $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>