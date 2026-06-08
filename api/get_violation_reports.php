<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../model/config/db_connect.php";
require_once __DIR__ . "/../model/dao/Report.php";
$ReportHandler = new Report($conn);
$reports = $ReportHandler->get_violation();
