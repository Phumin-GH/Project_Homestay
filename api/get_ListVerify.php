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
    $homestay = $verifyHandler->get_homestay();
}
