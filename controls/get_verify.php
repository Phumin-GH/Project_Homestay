<?php
// get_users.php

include("../config/db_connect.php");

if (isset($_SESSION["Admin_email"])) {
    $email = $_SESSION["Admin_email"];
    $stmt = $conn->prepare("SELECT * FROM user ");
    $stmt->execute([$email]);
    $stmt2 = $conn->prepare("SELECT * FROM host WHERE Host_Status = 2");
    $stmt1 = $conn->prepare("SELECT * FROM host WHERE Host_Status = 1");
    $host_ver = $stmt1->fetchAll(PDO::FETCH_ASSOC);
    $host_ban = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $users = [];
    $host_ver = [];
    $host_ban = [];
}

?>