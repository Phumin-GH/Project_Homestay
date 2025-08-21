<?php

include __DIR__ . "/../config/db_connect.php";

$pending_status = [];
$checkIn_status = [];
$checkOut_status = [];
$refund_status = [];
if(isset($_POST['Property_id'])){
    $property_id = $_POST['Property_id'];

    $Pending = "SELECT 'Online' AS Source,b.Booking_id,b.Property_id,p.Property_name,b.Room_id,u.Firstname,u.Lastname,
    u.Phone, b.Guests, b.Check_in, b.Check_out,b.Night,b.Total_price,b.Payment_status,b.Create_at,b.Check_status
    FROM booking b INNER JOIN user u ON b.User_id = u.User_id 
    INNER JOIN property p ON b.Property_id = p.Property_id

    WHERE b.Property_id = ? 
    UNION ALL 
    SELECT 'Walkin' AS Source,w.WalkIn_id AS Booking_id,w.Property_id,p.Property_name,w.Room_id,w.Firstname, w.Lastname,
    w.Phone,w.Guests, w.Check_in, w.Check_out,w.Night,w.Total_price,w.Payment_status,w.Create_at,w.Check_status
    FROM walkin w 
    INNER JOIN property p ON w.Property_id = p.Property_id
    
    WHERE w.Property_id = ? AND w.Check_status = 'Pending' ORDER BY `Check_in` 
    ";

    $Check_in = "SELECT 'Online' AS Source,b.Booking_id,b.Property_id,p.Property_name,b.Room_id,u.Firstname,u.Lastname,
    u.Phone, b.Guests, b.Check_in, b.Check_out,b.Night,b.Total_price,b.Payment_status,b.Create_at,b.Check_status
    FROM booking b INNER JOIN user u ON b.User_id = u.User_id 
    INNER JOIN property p ON b.Property_id = p.Property_id

    WHERE b.Property_id = ? 
    UNION ALL 
    SELECT 'Walkin' AS Source,w.WalkIn_id AS Booking_id,w.Property_id,p.Property_name,w.Room_id,w.Firstname, w.Lastname,
    w.Phone,w.Guests, w.Check_in, w.Check_out,w.Night,w.Total_price,w.Payment_status,w.Create_at,w.Check_status
    FROM walkin w 
    INNER JOIN property p ON w.Property_id = p.Property_id
    
    WHERE w.Property_id = ? AND w.Check_status = 'Checked_in' ORDER BY `Check_in` 
    ";
    $Check_out = "SELECT 'Online' AS Source,b.Booking_id,b.Property_id,p.Property_name,b.Room_id,u.Firstname,u.Lastname,
    u.Phone, b.Guests, b.Check_in, b.Check_out,b.Night,b.Total_price,b.Payment_status,b.Create_at,b.Check_status
    FROM booking b INNER JOIN user u ON b.User_id = u.User_id 
    INNER JOIN property p ON b.Property_id = p.Property_id

    WHERE b.Property_id = ? 
    UNION ALL 
    SELECT 'Walkin' AS Source,w.WalkIn_id AS Booking_id,w.Property_id,p.Property_name,w.Room_id,w.Firstname, w.Lastname,
    w.Phone,w.Guests, w.Check_in, w.Check_out,w.Night,w.Total_price,w.Payment_status,w.Create_at,w.Check_status
    FROM walkin w 
    INNER JOIN property p ON w.Property_id = p.Property_id
    
    WHERE w.Property_id = ? AND w.Check_status = 'Checked_out' ORDER BY `Check_in` 
    ";
    $Refund = "SELECT 'Online' AS Source,b.Booking_id,b.Property_id,p.Property_name,b.Room_id,u.Firstname,u.Lastname,
    u.Phone, b.Guests, b.Check_in, b.Check_out,b.Night,b.Total_price,b.Payment_status,b.Create_at,b.Check_status
    FROM booking b INNER JOIN user u ON b.User_id = u.User_id 
    INNER JOIN property p ON b.Property_id = p.Property_id

    WHERE b.Property_id = ? 
    UNION ALL 
    SELECT 'Walkin' AS Source,w.WalkIn_id AS Booking_id,w.Property_id,p.Property_name,w.Room_id,w.Firstname, w.Lastname,
    w.Phone,w.Guests, w.Check_in, w.Check_out,w.Night,w.Total_price,w.Payment_status,w.Create_at,w.Check_status
    FROM walkin w 
    INNER JOIN property p ON w.Property_id = p.Property_id
    
    WHERE w.Property_id = ? AND w.Check_status = 'Cancelled' ORDER BY `Check_in` 
    ";
    
    $stmt = $conn->prepare($Pending);
    $stmt ->execute([$property_id,$property_id]);
    $pending_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare($Check_in);
    $stmt ->execute([$property_id,$property_id]);
    $checkIn_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare($Check_out);
    $stmt ->execute([$property_id,$property_id]);
    $checkOut_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare($Refund);
    $stmt ->execute([$property_id,$property_id]);
    $refund_status = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header("Location: ../hosts/checkInOut.php");
    exit();
}