<?php
include '../config/db_connect.php';
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // ปิด warning/notice บนหน้า
header('Content-Type: application/json');

$property_id = (int)($_POST['property_id'] ?? 0);
$room_id = (int)($_POST['room_id'] ?? 0);
$check_in_date = $_POST['check_in_date'] ;
$check_out_date = $_POST['check_out_date'] ;
$f_name= $_POST['firstName'] ;
    $l_name = $_POST['lastName'] ;
    $phone = $_POST['guestsPhone'] ;
$nights = (int)($_POST['nights'] ?? 0);
$guests = (int)($_POST['guests'] ?? 0);
// $price = (int)($_POST['price'] ?? 0);



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
if (empty($_SESSION['Host_email'])) {
    echo json_encode(['error' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}
try {
    // $base_guests = $room['base_guests'] ?? 3;
    $stmt = $conn->prepare("SELECT Room_price AS price_per_night FROM room WHERE Room_id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$room) { echo json_encode(['error' => 'ไม่พบข้อมูลห้องพัก']); exit; }

    $price_per_night = $room['price_per_night'];
    $base_guests = 4;
    $extra_guest_fee = 200;
    $service_fee = 100;
    $total_price = $nights * $price_per_night;
    if ($guests > $base_guests) {
        $total_price += ($guests - $base_guests) * $extra_guest_fee * $nights;
    }
    $total_price += $service_fee;
    if ($total_price < 0) { $total_price = 0; }
    if(isset($_POST['submit_btn'])) {
    $message = '';
    // $total_price = (int)($_POST['total_price']) ?? 0;

    $insertSQL = "INSERT INTO walkin ( Property_id, Room_id,Firstname,Lastname, Phone,Check_in,Check_out,Night,Guests,Total_price) 
                  VALUES (?, ?, ?, ?, ?, ?, ?,?,?,?)";
    $stmt = $conn->prepare($insertSQL);
    $stmt->execute([ $property_id, $room_id,$f_name,$l_name, $phone,$check_in_date, $check_out_date, $nights,$guests, $total_price]);
    

    echo json_encode([
        'success' => true,
        'message' => 'ยืนยันสำเร็จ'
    ]);
    exit;
}else{
    echo json_encode([
            'total_price' => round($total_price, 2),
            'message' => ''
        ]);
        exit;
}
    
} catch (PDOException $e) {
   error_log("Error: " . $e->getMessage());
    echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    exit;
}




ob_end_flush();


?>