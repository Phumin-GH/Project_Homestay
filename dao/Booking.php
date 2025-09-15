<?php
class Booking
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล
    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    // Method สำหรับการเข้าสู่ระบบ
    public function book_online($email, $property_id, $room_id, $check_in_date, $check_out_date,  $nights, $guests, $total_price)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return  "ไม่พบผู้ใช้";
        }

        $insertSQL = "INSERT INTO booking 
            (User_id, Property_id, Room_id, Check_in, Check_out, Guests, Night, Total_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertSQL);
        $stmt->execute([$user['User_id'], $property_id, $room_id, $check_in_date, $check_out_date, $guests, $nights, $total_price]);
        $booking_id = $this->conn->lastInsertId();

        return $booking_id;
    }

    public function book_walkin($property_id, $room_id, $check_in_date, $check_out_date, $f_name, $l_name, $phone, $nights, $guests, $total_price)
    {
        if ($room_id <= 0 || $nights <= 0 || $guests <= 0) {
            return "กรุณาเลือกห้องพัก";
        }
        if (!$property_id || !$room_id || !$f_name || !$l_name || !$phone) {
            return "กรุณากรอกข้อมูล บ้านพัก ห้องและชื่อผู้เข้าพัก";
        }
        $today = date('Y-m-d');
        if ($check_in_date < $today || empty($check_in_date) || empty($check_out_date) || strtotime($check_out_date) <= strtotime($check_in_date)) {
            return "กรุณากรอกวันที่ให้ถูกต้อง";
        }
        $insertSQL = "INSERT INTO walkin ( Property_id, Room_id,Firstname,Lastname, Phone,Check_in,Check_out,Night,Guests,Total_price,Payment_status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?,?,?,?,?)";
        $stmt = $this->conn->prepare($insertSQL);
        $stmt->execute([$property_id, $room_id, $f_name, $l_name, $phone, $check_in_date, $check_out_date, $nights, $guests, $total_price, 'paid']);
        return true;
    }
    public function calcuratePrice($room_id, $nights, $guests)
    {
        if (!$room_id || !$nights  || !$guests) {
            return  "กรุณาเลือกห้องพัก";
        }
        $stmt = $this->conn->prepare("SELECT Room_price FROM room WHERE Room_id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$room) {
            return "ไม่พบข้อมูลห้องพักที่เลือก";
        }
        $price_per_night = $room['Room_price'];
        $base_guests = 4;
        $extra_guest_fee = 200;
        $service_fee = 100;
        $total_price = $nights * $price_per_night;
        if ($guests > $base_guests) {
            $total_price += ($guests - $base_guests) * $extra_guest_fee * $nights;
        }
        $total_price += $service_fee;
        if ($total_price < 0) {
            $total_price = 0;
        }
        return $total_price;
    }

    public function TypeGateway($method)
    {
        if ($method === 'credit-card') {
            return "credit-card";
        } else if ($method === 'qrcode') {
            return "qrcode";
        } else {
            return false;
        }
    }
    public function PaymentStatus($charge_id, $payment_status, $qrcode, $booking_id, $booking_status)
    {
        if (!$charge_id || !$payment_status || !$qrcode || !$booking_id || !$booking_status) {
            return ['success' => false, 'message' => 'ไม่มีข้อมูลการจอง'];
        }
        $sql = "UPDATE booking SET Charge_id = ?,Booking_status = ?,Payment_gateway = ?,
        Payment_status = ?,Booking_qrcode = ? WHERE Booking_id = ?";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$charge_id, $booking_status, 'Orcode', $payment_status, $qrcode, $booking_id,]);
            return ['success' => true, 'message' => 'ชำระเงินสำเร็จ!'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function get_Booking($email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?  ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        $stmt = $this->conn->prepare("SELECT p.Property_name,b.User_id,p.*,r.*,h.Host_firstname,h.Host_lastname,h.Host_phone,b.Check_in,b.Check_out,b.Guests,b.Night,b.Booking_status,b.Total_price
        ,b.Payment_status,b.Create_at,b.Check_status FROM booking b 
        INNER JOIN property p ON b.Property_id = p.Property_id 
        INNER JOIN room r ON b.Room_id = r.Room_id
        INNER JOIN host h ON p.Host_id = h.Host_id
        
        WHERE User_id = ?  && Booking_status= 'successful'");
        $stmt->execute([$user['User_id']]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $bookings;
    }
    public function get_HistoryBook($email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?  ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        $stmt = $this->conn->prepare("SELECT b.User_id,p.*,r.*,h.Host_firstname,h.Host_lastname,h.Host_phone,b.Check_in,b.Check_out,b.Guests,b.Night,b.Booking_status,b.Total_price
        ,b.Payment_status,b.Create_at,b.Check_status FROM booking b 
        INNER JOIN property p ON b.Property_id = p.Property_id 
        INNER JOIN room r ON b.Room_id = r.Room_id
        INNER JOIN host h ON p.Host_id = h.Host_id
        WHERE User_id = ?  && Booking_status= 'successful' && Check_status = 'Checked_out'");
        $stmt->execute([$user['User_id']]);
        $history_booking = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $history_booking;
    }
}
