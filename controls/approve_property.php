<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Verify.php';
$verifyHandler = new Verify($conn);
if (empty($_SESSION['Admin_email'])) {
    header("Location: ../views/admin/admin-login.php");
    exit();
}
if (!isset($_GET['id'])) {
    echo "ไม่พบรหัสบ้านพัก";
    header("Location: ../views/admin/approve-properties.php");
    exit();
}
$property_id = $_GET['id'];
if (isset($_POST['approve'])) {
    $result = $verifyHandler->approve_property($property_id);
    $_SESSION['msg'] = '<div class="alert alert-success">อนุมัติเรียบร้อย</div>';
    header("Location: ../views/admin/approve-properties.php");
    exit();
}
if (isset($_POST['cancel'])) {
    $result = $verifyHandler->cancel_property($property_id);
    $_SESSION['error1'] = '<div class="alert alert-danger">เกิดข้อผิดพลาด: </div>';
    header("Location: ../views/admin/approve-properties.php");
    exit();
}
