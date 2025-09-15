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
    $stmt = $conn->prepare("SELECT * FROM host WHERE Host_status = 'pending_verify'");
    $stmt->execute();
    $verify_host = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM host WHERE Host_status = 'cancel'");
    $stmt->execute();
    $ban_host = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM host WHERE Host_status = 'active'");
    $stmt->execute();
    $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM admin_sys WHERE Admin_email = ?");
    $stmt->execute([$email]);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM user WHERE User_Status = 'active'");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare("SELECT * FROM user WHERE User_Status = 'banned'");
    $stmt->execute();
    $ban_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $conn->prepare("SELECT * FROM user WHERE User_Status = 'inactive'");
    $stmt->execute();
    $inactive_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
?>