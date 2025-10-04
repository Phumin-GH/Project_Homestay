<?php
require_once __DIR__ . "/model/config/db_connect.php";

$sql = "SELECT b.Booking_id,r.Room_number,b.Room_id, b.User_id, b.Check_in, b.Check_out 
        FROM booking b
        INNER JOIN room r ON b.Room_id = r.Room_id
        WHERE b.Property_id = 1";
$stmt = $conn->prepare($sql);
$stmt->execute();
$bookings_from_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events = [];
foreach ($bookings_from_db as $booking) {
    $events[] = [
        'title'  => 'ห้อง' . $booking['Room_number'],
        'start'  => $booking['Check_in'],
        'end'    => $booking['Check_out'],
        'allDay' => true,
        'backgroundColor' => '#ff5733',
        'borderColor' => '#c70039',
        'textColor' => '#fff'

    ];
}

header('Content-Type: application/json');
echo json_encode($events, JSON_UNESCAPED_UNICODE);
