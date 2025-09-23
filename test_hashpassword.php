<?php
require_once __DIR__ . '/model/config/db_connect.php';
// $password = 'phumin.pm2003';
// $hashed_password = password_hash($password, PASSWORD_DEFAULT);
// echo $hashed_password;
// $hash = '$2y$10$F1ny6.wfBvZlS9OuS9KRsuVo.O2ee13GQMuuKu4TKtrocG2qzkOb6';
// $verify = password_verify('phumin.pm2003', $hash);
// if ($verify === true) {
//     echo "true";
// } else {
//     echo "false";
// }
// date_default_timezone_set("Asia/Bangkok");
// $expires = date("Y-m-d");
// echo $expires;
$booking_id = 196;
$sql = "SELECT  DATEDIFF(b.Check_in,r.Refund_date) AS nights 
        FROM booking b INNER JOIN refund r ON b.Booking_id = r.Booking_id WHERE b.Booking_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$booking_id]);
$nights = $stmt->fetchColumn();
echo $nights;
$sql = "SELECT Before_checkin,Refund_percen FROM refund_policy ORDER BY Before_checkin ASC";
$stmt = $conn->prepare($sql);
$stmt->execute();
$policy = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($policy as $row) {
    if ($nights >= $row['Before_checkin']) {

        echo $row['Refund_percen'];
    }
}
echo "------------";

$percen_amount = 1500 * ($row['Refund_percen'] / 100);
echo $percen_amount;
echo $row['Refund_percen'];
