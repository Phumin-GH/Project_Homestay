<?php
if (session_start() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once  __DIR__ . "/../model/config/db_connect.php";
require_once  __DIR__ . "/../model/dao/Review.php";
try {
    $reviewsHandler = new Review($conn);
    if (isset($_POST['submit_del_rv'])) {
        if (empty($_POST['review_id'])) {
            echo json_encode(["message" => "ไม่พบข้อมูลรีวิว", "success" => false]);
            exit();
        }
        $review_id = $_POST['review_id'];
        $result = $reviewsHandler->deleteReview($review_id);
        if ($result === true) {
            echo json_encode(["message" => "ลบรีวิวสำเร็จ", "success" => true]);
            exit();
        } else {
            echo json_encode(["message" => "ไม่สามารถลบรีวิวได้", "success" => false]);
            exit();
        }
    }
    if (isset($_POST['submit_hidden_rv'])) {
        if (empty($_POST['review_id'])) {
            echo json_encode(["message" => "ไม่พบข้อมูลรีวิว", "success" => false]);
            exit();
        }
        $review_id = $_POST['review_id'];
        $result = $reviewsHandler->hidden_review($review_id);
        if ($result === true) {
            echo json_encode(["message" => "ซ่อนรีวิวสำเร็จ", "success" => true]);
            exit();
        } else {
            echo json_encode(["message" => $result, "success" => false]);
            exit();
        }
    }
    if (isset($_POST['submit_show_rv'])) {
        if (empty($_POST['review_id'])) {
            echo json_encode(["message" => "ไม่พบข้อมูลรีวิว", "success" => false]);
            exit();
        }
        $review_id = $_POST['review_id'];
        $result = $reviewsHandler->show_review($review_id);
        if ($result === true) {
            echo json_encode(["message" => "แสดงรีวิวสำเร็จ", "success" => true]);
            exit();
        } else {
            echo json_encode(["message" => $result, "success" => false]);
            exit();
        }
    }
    if (isset($_POST['submit_rv_user'])) {
        if (isset($_SESSION['User_email'])) {
            if (empty($_POST['review_id']) || empty(trim($_POST['reason']))) {
                echo json_encode(['message' => "ข้อมูลไม่ครบถ้วน, กรุณากรอกเหตุผล", "success" => false]);
                exit();
            }
            $user_email = $_SESSION['User_email'];
            $review_id = (int)$_POST['review_id'];
            $reason = trim($_POST['reason']);
            $host_email = null;
            $result = $reviewsHandler->violation($review_id, $user_email, $host_email, $reason);
            if ($result === true) {
                echo json_encode(['message' => "รอทางแอดมินตรวจสอบ", "success" => true]);
                exit();
            } else {
                echo json_encode(['message' => $result, "success" => false]);
                exit();
            }
        } else {
            echo json_encode(['message' => "ไม่พบการรายงาน", "success" => false]);
            exit();
        }
    }
    if (isset($_POST['submit_rv_host'])) {
        if (isset($_SESSION['Host_email'])) {
            if (empty($_POST['review_id']) || empty(trim($_POST['reason']))) {
                echo json_encode(['message' => "ข้อมูลไม่ครบถ้วน, กรุณากรอกเหตุผล", "success" => false]);
                exit();
            }
            $host_email = $_SESSION['Host_email'];
            $review_id = (int)$_POST['review_id'];
            $reason = trim($_POST['reason']);
            $user_email = null;
            $result = $reviewsHandler->violation($review_id, $user_email, $host_email, $reason);
            if ($result === true) {
                echo json_encode(['message' => "รอทางแอดมินตรวจสอบ", "success" => true]);
                exit();
            } else {
                echo json_encode(['message' => $result, "success" => false]);
                exit();
            }
        } else {
            echo json_encode(['message' => "ไม่พบการรายงาน", "success" => false]);
            exit();
        }
    }
} catch (Exception $e) {
    $e->getMessage();
}