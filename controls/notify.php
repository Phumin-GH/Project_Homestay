<?php
header('Content-Type: application/json');
include __DIR__ . "/../config/db_connect.php";

if (isset($_POST['property_id'])) {
    $property_id = $_POST['property_id'];

    $sql = "
        SELECT COUNT(*) as total 
        FROM (
            SELECT b.Check_in 
            FROM booking b 
            WHERE b.Property_id = ? AND DATE(b.Check_in) >= CURDATE()
            UNION ALL
            SELECT w.Check_in 
            FROM walkin w 
            WHERE w.Property_id = ? AND DATE(w.Check_in) >= CURDATE()
        ) AS upcoming
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$property_id, $property_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['total' => $row['total']]);
}