<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Review.php';
$reviewsHandler = new Review($conn);
if (isset($_POST['house_id'])) {
    $house_id = $POST['house_id'] ?? '';
    $reviews = [];
    $reviews = $reviewsHandler->get_Reviews($house_id);
    if (is_string($reviews)) {
        $_SESSION['err'] = "ไม่พบ ID";
    }
}