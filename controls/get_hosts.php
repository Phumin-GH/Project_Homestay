<?php
include("../config/db_connect.php");

if (isset($_SESSION["Host_email"])) {
    $email = $_SESSION["Host_email"];
    $stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ?");
    $stmt->execute([$email]);
    $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if((isset($_SESSION["Admin_email"]))){
    $email = $_SESSION["Admin_email"];
    $stmt = $conn->prepare("SELECT * FROM host WHERE Host_status = 0");
    $stmt->execute();
    $verify_host = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM host WHERE Host_status = 2");
    $stmt->execute();
    $ban_host = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM host WHERE Host_status = 1");
    $stmt->execute();
    $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM admin_sys WHERE Admin_email = ?");
    $stmt->execute([$email]);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
?>