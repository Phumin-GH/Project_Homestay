<?php
include '../config/db_connect.php';

header('Content-Type: application/json');
if (isset($_GET['Property_id'])) {
    $property_id = $_GET['Property_id'];

    $stmt = $conn->prepare("SELECT Room_id, Room_number,Room_price FROM room WHERE Property_id = ? AND Room_status = '0'");
    $stmt->execute([$property_id]);
    $room = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($room);
    exit();
}
?>