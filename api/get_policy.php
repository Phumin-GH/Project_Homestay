<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Policy.php';

$policyHandler = new Policy($conn);

// ตรวจสอบว่าต้องการข้อมูลเฉพาะ ID หรือทั้งหมด
if (isset($_GET['id'])) {
    $policy_id = (int) $_GET['id'];
    $policy = $policyHandler->get_Policy($policy_id);
} elseif (isset($_GET['stats'])) {
    // ดึงสถิติการใช้งาน
    $policy = $policyHandler->get_Policy_Stats();
} else {
    // ดึงข้อมูลทั้งหมด
    $policy = $policyHandler->get_Policy();
}

echo json_encode($policy, JSON_UNESCAPED_UNICODE);
exit();