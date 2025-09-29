<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../model/config/db_connect.php";
require_once __DIR__ . "/../model/dao/Review.php";
$ReportHandler = new Review($conn);
$reports = $ReportHandler->get_violation();