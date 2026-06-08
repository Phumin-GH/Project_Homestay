<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION["Admin_email"])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
    exit();
}

require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Policy.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$PolicyHandler = new Policy($conn);
$action = $_POST['action'] ?? '';
$email = $_SESSION['Admin_email'];

try {
    switch ($action) {
        case 'create':
            $before_checkin = (int) ($_POST['before_checkin'] ?? 0);
            $refund_percent = (int) ($_POST['refund_percent'] ?? 0);
            $description = $_POST['policy_description'] ?? '';

            // Validation
            if ($before_checkin < 0) {
                echo json_encode(['success' => false, 'message' => 'จำนวนวันต้องไม่น้อยกว่า 0']);
                exit();
            }

            if ($refund_percent < 0 || $refund_percent > 100) {
                echo json_encode(['success' => false, 'message' => 'เปอร์เซ็นต์ต้องอยู่ระหว่าง 0-100']);
                exit();
            }

            $result = $PolicyHandler->Add_Policy($before_checkin, $refund_percent, $description, $email);

            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'เพิ่มนโยบายเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['success' => false, 'message' => $result]);
            }
            break;

        case 'update':
            $policy_id = (int) ($_POST['policy_id'] ?? 0);
            $before_checkin = (int) ($_POST['before_checkin'] ?? 0);
            $refund_percent = (int) ($_POST['refund_percent'] ?? 0);
            $description = $_POST['policy_description'] ?? '';

            // Validation
            if ($policy_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID นโยบายไม่ถูกต้อง']);
                exit();
            }

            if ($before_checkin < 0) {
                echo json_encode(['success' => false, 'message' => 'จำนวนวันต้องไม่น้อยกว่า 0']);
                exit();
            }

            if ($refund_percent < 0 || $refund_percent > 100) {
                echo json_encode(['success' => false, 'message' => 'เปอร์เซ็นต์ต้องอยู่ระหว่าง 0-100']);
                exit();
            }

            $result = $PolicyHandler->Update_Policy($policy_id, $before_checkin, $refund_percent, $description, $email);

            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'แก้ไขนโยบายเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['success' => false, 'message' => $result]);
            }
            break;

        case 'delete':
            $policy_id = (int) ($_POST['policy_id'] ?? 0);

            if ($policy_id <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID นโยบายไม่ถูกต้อง']);
                exit();
            }

            $result = $PolicyHandler->Delete_Policy($policy_id);

            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'ลบนโยบายเรียบร้อยแล้ว']);
            } else {
                echo json_encode(['success' => false, 'message' => $result]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'การดำเนินการไม่ถูกต้อง']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>