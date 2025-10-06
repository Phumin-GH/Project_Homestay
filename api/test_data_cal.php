<?php
require_once __DIR__ . "/../model/config/db_connect.php";
require_once __DIR__ . "/../model/dao/Property.php";
$calendar = new Property($conn);
$property_id = (int)$_GET['id'] ?? 0;
$bookings_from_db = $calendar->Calendar_Room($property_id);

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
