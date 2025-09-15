<?php

include __DIR__ . '/../config/db_connect.php';

if (empty($_SESSION['User_email'])) {
    header("Location: user-login.php");
}
$message = '';

if (isset($_SESSION['User_email'])) {
    $email = $_SESSION['User_email'];
    $stmt = $conn->prepare("SELECT User_id FROM user WHERE User_email = ?  ");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {


        $stmt = $conn->prepare("SELECT b.User_id,p.*,r.*,h.Host_firstname,h.Host_lastname,h.Host_phone,b.Check_in,b.Check_out,b.Guests,b.Night,b.Booking_status,b.Total_price
        ,b.Payment_status,b.Create_at,b.Check_status FROM booking b 
        INNER JOIN property p ON b.Property_id = p.Property_id 
        INNER JOIN room r ON b.Room_id = r.Room_id
        INNER JOIN host h ON p.Host_id = h.Host_id

        WHERE User_id = ?  && Booking_status= 'successful' && Check_status = 'Checked_out'");
        $stmt->execute([$user['User_id']]);
        $history_booking = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
