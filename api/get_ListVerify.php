<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Verify.php';
$verifyHandler = new Verify($conn);

if (isset($_SESSION['Admin_email'])) {
    $email = $_SESSION['Admin_email'];
    $verify_host = $verifyHandler->get_verify_hosts();
    $ban_host = $verifyHandler->get_ban_hosts();
    $cancel_host = $verifyHandler->get_cancel_hosts();
    $hosts = $verifyHandler->get_hosts();
    $admins = $verifyHandler->get_admins($email);
    $users = $verifyHandler->get_users();
    $ban_user = $verifyHandler->get_ban_user();
    $inactive_user = $verifyHandler->get_inactive_user();
    $homestayRowData = $verifyHandler->get_homestay();
    $homestay = [];
    foreach ($homestayRowData as $row) {
        $propertyId = $row['Property_id'];
        if (!isset($homestay[$propertyId])) {
            $homestay[$propertyId] = [
                'Property_id' => $row['Property_id'],
                'Property_name' => $row['Property_name'],
                'Property_province' => $row['Property_province'],
                'Property_district' => $row['Property_district'],
                'Property_subdistrict' => $row['Property_subdistrict'],
                'Property_latitude' => $row['Property_latitude'],
                'Property_longitude' => $row['Property_longitude'],
                'Property_image' => $row['Property_image'],
                'Host_firstname' => $row['Host_firstname'],
                'Host_lastname' => $row['Host_lastname'],
                'Host_email' => $row['Host_email'],
                'Host_phone' => $row['Host_phone'],
                'rooms' => [] // สร้าง Array ว่างสำหรับเก็บห้องพัก
            ];
        }
        $homestay[$propertyId]['rooms'][] = [
            'Room_number' => $row['Room_number'],
            'Room_price' => $row['Room_price'],
            'Room_capacity' => $row['Room_capacity'],
            'Room_utensils' => $row['Room_utensils'],
            'Room_status' => $row['Room_status']
        ];
    }
}