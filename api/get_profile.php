<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/User.php';
require_once __DIR__ . '/../model/dao/Admin.php';
require_once __DIR__ . '/../model/dao/Host.php';
$UserHandler = new User($conn);
$HostHandler = new Host($conn);
$AdminHandler = new Admin($conn);
if (isset($_SESSION['User_email'])) {
    $email = $_SESSION['User_email'];
    $profile = $UserHandler->getDataUser($email);
}
