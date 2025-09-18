<?php
header('Content-Type: application/json');
include __DIR__ . "/../../config/db_connect.php";

if (isset($_POST['property_id'])) {
  $property_id = $_POST['property_id'];
  echo json_encode(['total' => $row['total']]);
}
