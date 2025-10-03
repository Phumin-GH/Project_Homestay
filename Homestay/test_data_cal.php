<?php
require_once __DIR__ . "/model/config/db_connect.php";

$sql = "SELECT Booking_id,Room_id, User_id, Check_in, Check_out 
        FROM booking 
        WHERE Property_id = 7";
$stmt = $conn->prepare($sql);
$stmt->execute();
$bookings_from_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = [];
foreach ($bookings_from_db as $booking) {
    $events[] = [
        'title'  => 'ห้อง' . $booking['Room_id'],
        'start'  => $booking['Check_in'],
        'end'    => $booking['Check_out'],
        'allDay' => true,

    ];
}

header('Content-Type: application/json');
echo json_encode($events);