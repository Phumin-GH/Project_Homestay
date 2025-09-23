<?php
header('Content-Type: application/json');
require_once  __DIR__ . "/../model/config/db_connect.php";
require_once  __DIR__ . "/../model/dao/Booking.php";
$notify = new Booking($conn);
if (isset($_POST['property_id'])) {
  $property_id = $_POST['property_id'];
  $row = $notify->notify($property_id);
  echo json_encode(['total' => $row['total']]);
}
