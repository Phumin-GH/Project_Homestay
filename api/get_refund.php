<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Refund.php';
$refundHandler = new Refund($conn);
$refund = $refundHandler->get_listRefund();
$refund_complete = $refundHandler->get_listComplete();
$refund_failed = $refundHandler->get_listFailed();