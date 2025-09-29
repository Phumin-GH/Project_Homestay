<?php
if (session_start() === PHP_SESSION_NONE) {
  session_start();
}
header('Content-Type: application/json');
require_once  __DIR__ . "/../model/config/db_connect.php";
require_once  __DIR__ . "/../model/dao/Booking.php";
require_once  __DIR__ . "/../model/dao/Review.php";
$notify = new Booking($conn);
$reviews = new Review($conn);
if (isset($_POST['property_id'])) {
  $property_id = $_POST['property_id'];
  $row = $notify->notify($property_id);
  echo json_encode(['total' => $row['total']]);
  exit();
}
if (isset($_POST['submit_rv'])) {
  $user_email = null;
  $host_email = null;
  if (!empty($_SESSION['User_email'])) {
    $user_email = $_SESSION['User_email'];
  } elseif (!empty($_SESSION['Host_email'])) {
    $host_email = $_SESSION['Host_email'];
  } else {
    echo json_encode([
      'success' => false,
      'message' => 'ไม่พบข้อมูลผู้ใช้งาน กรุณาเข้าสู่ระบบใหม่'
    ]);
    exit;
  }
  $review_id = $_POST['review_id'];
  $reason = $_POST['reason'];
  if (empty($review_id) || empty($reason)) {
    echo json_encode(['message' => "ข้อมูลไม่ครบถ้วน, กรุณากรอกเหตุผล", "success" => false]);
    exit();
  }
  $result = $reviews->violation($review_id, $user_email, $host_email, $reason);
  if ($result === true) {
    echo json_encode(['message' => "รอทางแอดมินตรวจสอบ", "success" => true]);
    exit();
  } else {
    echo json_encode(['message' => $result, "success" => false]);
    exit();
  }
}