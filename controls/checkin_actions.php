<?php


include __DIR__ . "/../model/config/db_connect.php";
header('Content-Type: application/json');

try {
    $book_id = $_POST['book_id'] ?? null;
    $action  = $_POST['action'] ?? null;
    $source  = $_POST['source'] ?? null;
    if (!$book_id || !$action || !$source) {
        throw new Exception("ข้อมูลไม่ครบ");
    }
    if ($action === 'Checked_in') {
        if ($source === 'Online') {
            $sql = "UPDATE booking SET Check_status = 'Checked_in' WHERE Booking_id = ?";
        } else {
            $sql = "UPDATE walkin SET Check_status = 'Checked_in' WHERE WalkIn_id = ?";
        }
    }
    if ($action === 'Checked_out') {
        if ($source === 'Online') {
            $sql = "UPDATE booking SET Check_status = 'Checked_out' WHERE Booking_id = ?";
        } else {
            $sql = "UPDATE walkin SET Check_status = 'Checked_out' WHERE WalkIn_id = ?";
        }
    }
    if ($action === 'Cancelled') {
        if ($source === 'Online') {
            $sql = "UPDATE booking SET Check_status = 'Cancelled' WHERE Booking_id = ?";
        } else {
            $sql = "UPDATE walkin SET Check_status = 'Cancelled' WHERE WalkIn_id = ?";
        }
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute([$book_id]);
    echo json_encode(['success' => true, 'message' => ucfirst($action) . ' สำเร็จ']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
