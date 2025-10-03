<?php
if (session_start() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION["User_email"])) {
    header("Location: ../index.php");
    exit();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Review.php';
$ReviewHandler = new Review($conn);
if (isset($_POST['submit_review'])) {
    $rating = (int)($_POST['review_rating'] ?? 0);
    $comment = trim($_POST['review_text'] ?? '');
    $property_id = (int)($_POST['property_id'] ?? 0);
    $user_email = $_SESSION['User_email'];
    if ($rating < 1) {
        echo json_encode(['success' => false, 'message' => 'โปรดให้คะแนนรีวิวบ้านพัก']);
        exit();
    }
    if (empty($comment)) {
        echo json_encode(['success' => false, 'message' => 'กรุณาใส่ความคิดเห็น']);
        exit();
    }
    if (!$property_id) {
        echo json_encode(['success' => false, 'message' => 'รหัสบ้านพักไม่ถูกต้อง']);
        exit();
    }
    $result = $ReviewHandler->addReview($user_email, $property_id, $rating, $comment);
    if ($result === true) {
        echo json_encode(['success' => true, 'message' => 'ส่งรีวิวเรียบร้อยแล้ว']);
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => $result]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}