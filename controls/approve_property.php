<?php
session_start();
include '../config/db_connect.php';
if (empty($_SESSION['Admin_email'])) {
    header("Location: admin-login.php");
    exit();
}
if (!isset($_GET['id'])) {
    echo "ไม่พบรหัสบ้านพัก";
    header("Location: ../admin/approve-properties.php");
    exit();
}
$property_id = $_GET['id'];
if(isset($_POST['approve'])){
    $status=1;
    $updateSQL = "UPDATE property SET Property_status=? WHERE Property_id = ?";
    $stmt = $conn->prepare($updateSQL);
    $stmt->execute([$status,$property_id]);
    $_SESSION['msg'] = '<div class="alert alert-success">อนุมัติเรียบร้อย</div>';
    header("Location: ../admin/approve-properties.php");
    exit();
}
if(isset($_POST['cancel'])){
    $status=2;
    $updateSQL = "UPDATE property SET Property_status=? WHERE Property_id = ?";
    $stmt = $conn->prepare($updateSQL);
    $stmt->execute([$status,$property_id]);
    $_SESSION['error1'] = '<div class="alert alert-danger">เกิดข้อผิดพลาด: </div>';
    header("Location: ../admin/approve-properties.php");
    exit();
}
?>