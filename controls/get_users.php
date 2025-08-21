<?php
// get_users.php

include("../config/db_connect.php");

if (isset($_SESSION["User_email"])) {
    $email = $_SESSION["User_email"];
    $sql = "SELECT * FROM user WHERE User_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $users = [];
}

?>