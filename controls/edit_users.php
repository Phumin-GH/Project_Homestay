<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Host.php';
require_once __DIR__ . '/../model/dao/User.php';
$hostHandler = new Host($conn);
$userHandler = new User($conn);
if (isset($_POST['edit_host'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $id = (int)($_POST['host_id']);
    if (!$firstname || !$lastname || !$phone) {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล"]);
    } else {
        $result = $hostHandler->admin_edit_host($firstname, $lastname, $phone, $id);
        if ($result === true) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
        } else {

            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        }
    }
    exit();
}
if (isset($_POST['edit_user'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $phone = trim($_POST['phone']);
    $id = (int)($_POST['user_id']);
    if (!$firstname || !$lastname || !$phone) {
        echo json_encode(["success" => false, "message" => "ไม่พบข้อมูล"]);
    } else {
        $result = $userHandler->admin_edit_user($firstname, $lastname, $phone, $id);
        if ($result === true) {
            echo json_encode(["success" => true, "message" => "แก้ไขข้อมูลเรียบร้อย"]);
        } else {

            echo json_encode(["success" => false, "message" => "ไม่สามารถแก้ไขข้อมูลได้"]);
        }
    }
    exit();
}