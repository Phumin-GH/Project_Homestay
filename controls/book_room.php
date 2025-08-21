<?php

include '../config/db_connect.php';
ob_start();
header('Content-Type: application/json');

$property_id = (int)($_POST['property_id'] ?? 0);
$room_id = (int)($_POST['room_id'] ?? 0);
$check_in_date = $_POST['check_in_date'] ;
$check_out_date = $_POST['check_out_date'] ;
$nights = (int)($_POST['nights'] ?? 0);
$guests = (int)($_POST['guests'] ?? 0);
$total = '';
error_log("Received POST data: " . json_encode($_POST));
if ($room_id <= 0) {
    echo json_encode(['error' => 'กรุณาเลือกห้องพัก']);
    exit;}
if (empty($check_in_date) || empty($check_out_date)) {
    echo json_encode(['error' => 'กรุณาเลือกวันที่เช็คอินและเช็คเอาท์']);
    exit;}
if ($nights <= 0) {
    echo json_encode(['error' => 'จำนวนคืนต้องมากกว่า 0']);
    exit;}
if ($guests <= 0) {
    echo json_encode(['error' => 'จำนวนผู้เข้าพักต้องมากกว่า 0']);
    exit;}
$today = date('Y-m-d');
if ($check_in_date < $today) {
    echo json_encode(['error' => 'วันที่เช็คอินต้องเป็นวันในอนาคต']);
    exit;}
if (strtotime($check_out_date) <= strtotime($check_in_date)) {
    echo json_encode(['error' => 'วันที่เช็คเอาท์ต้องอยู่หลังวันที่เช็คอิน']);
    exit;}
try {
    $stmt = $conn->prepare("SELECT Room_price AS price_per_night FROM room WHERE Room_id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$room) {
        echo json_encode(['error' => 'ไม่พบข้อมูลห้องพัก']);
        exit;}
    $price_per_night = $room['price_per_night'];
    // $base_guests = $room['base_guests'] ?? 3;
    $base_guests =  4;
    $extra_guest_fee = 200;
    $service_fee = 100;
    $total_price = $nights * $price_per_night;
    if ($guests > $base_guests) {
        $total_price += ($guests - $base_guests) * $extra_guest_fee * $nights;}
    $total_price += $service_fee;
    $total =$total_price;
    if ($total_price < 0) {
        $total_price = 0;}
    error_log("Total price calculated: " . $total_price);
    echo json_encode([
        'total_price' => round($total_price, 2),
        'message' => '']);
} catch (PDOException $e) {
    error_log("Error retrieving room: " . $e->getMessage());
    echo json_encode(['error' => 'Error retrieving room: ' . $e->getMessage()]);
    exit;
}

session_start();
if (empty($_SESSION['User_email'])) {
    echo json_encode(['error' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}
$message = '';
if (isset($_POST['bookBtn'])) {
        $email = $_SESSION['User_email'];
    $stmt = $conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo json_encode(['error' => 'ไม่พบผู้ใช้']);
        exit;
    }

    $insertSQL = "INSERT INTO booking (User_id, Property_id, Room_id, Check_in, Check_out, Guests, Total_price) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSQL);
    $stmt->execute([$user['User_id'], $property_id, $room_id, $check_in_date, $check_out_date, $guests, $total_price]);
    $booking_id = $conn->lastInsertId();
    error_log("Booking ID: " . $booking_id);

    echo json_encode([
        'success' => true,
        'booking_id' => $booking_id,
        'message' => 'จองสำเร็จ'
    ]);
    exit;
}
ob_end_flush();


?>