<?php
class Booking
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล
    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function total_income()
    {
        $sql = "SELECT SUM(Booking_service + Booking_vat)AS Total FROM booking WHERE Payment_status = 'paid' AND Booking_status = 'successful'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $total_income = $stmt->fetchColumn();
        return $total_income;
    }
    public function total_booking()
    {
        $sql = "SELECT COUNT(*) FROM booking";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $total_booking = $stmt->fetchColumn();
        return $total_booking;
    }
    // Method สำหรับการเข้าสู่ระบบ
    public function book_online($email, $property_id, $room_id, $check_in_date, $check_out_date,  $nights, $guests, $total_price, $service, $vat)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user || !$service || !$vat) {
            return  "ไม่พบข้อมูลผู้ใช้หรือค่าบริการ/ภาษีไม่ถูกต้อง";
        }
        $insertSQL = "INSERT INTO booking 
            (User_id, Property_id, Room_id, Check_in, Check_out, Guests, Night, Total_price,Booking_service,Booking_vat) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertSQL);
        $stmt->execute([$user['User_id'], $property_id, $room_id, $check_in_date, $check_out_date, $guests, $nights, $total_price, $service, $vat]);
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
        // $service_fee = 100;
        $total_price = $nights * $price_per_night;
        if ($guests > $base_guests) {
            $total_price += ($guests - $base_guests) * $extra_guest_fee * $nights;
        }
        // $total_price += $service_fee;
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
    public function delete_Booking($booking_id)
    {

        $sql = "DELETE FROM  booking  WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id]);
        return true;
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
        $stmt = $this->conn->prepare("SELECT p.Property_name,b.Booking_id,b.User_id,p.*,r.*,h.Host_firstname,h.Host_lastname,h.Host_phone,b.Check_in,b.Check_out,b.Guests,b.Night,b.Booking_status,b.Total_price
        ,b.Payment_status,b.Create_at,b.Check_status FROM booking b 
        INNER JOIN property p ON b.Property_id = p.Property_id 
        INNER JOIN room r ON b.Room_id = r.Room_id
        INNER JOIN host h ON p.Host_id = h.Host_id
        WHERE User_id = ?  && Booking_status= 'successful' && Check_status = 'Pending'
        ORDER BY b.Check_in DESC");
        $stmt->execute([$user['User_id']]);
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $bookings;
    }
    public function get_HistoryBook($email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?  ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $this->conn->prepare("SELECT b.User_id,p.*,r.*,h.Host_firstname,h.Host_lastname,h.Host_phone,b.Check_in,b.Check_out,b.Guests,b.Night,b.Booking_status,b.Total_price
        ,b.Payment_status,b.Create_at,b.Check_status FROM booking b 
        INNER JOIN property p ON b.Property_id = p.Property_id 
        INNER JOIN room r ON b.Room_id = r.Room_id
        INNER JOIN host h ON p.Host_id = h.Host_id
        WHERE User_id = ?  && Booking_status= 'successful' && Check_status = 'Checked_out'
        ORDER BY b.Create_at DESC");
        $stmt->execute([$user['User_id']]);
        $history_booking = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $history_booking;
    }
    public function get_CancelBook($email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?  ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->conn->prepare("SELECT b.User_id,p.Property_name,
        p.Property_province,p.Property_district,p.Property_subdistrict,p.Property_image
        ,b.Booking_id,h.Host_firstname,h.Host_lastname,h.Host_phone,b.Check_in,b.Check_out
        ,b.Guests,b.Night,b.Booking_status,b.Total_price
        ,b.Payment_status,b.Create_at,b.Check_status,rf.Refund_status,rf.Host_check FROM booking b 
        INNER JOIN property p ON b.Property_id = p.Property_id 
        INNER JOIN host h ON p.Host_id = h.Host_id
        INNER JOIN refund rf ON rf.Host_id = h.Host_id
        WHERE b.User_id = ? && b.Booking_status= 'failed' && Payment_status = 'paid'
        ORDER BY b.Create_at DESC");
        $stmt->execute([$user['User_id']]);
        $cancel_booking = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $cancel_booking;
    }
    public function  check_Calender($property_id, $room_id, $check_in, $check_out)
    {
        // if (!$room_id || !$check_in || !$check_out) {
        //     return "กรุณาเลือกห้องพักและวันที่";
        // }
        // $stmt = $this->conn->prepare("SELECT COUNT(*) FROM booking 
        // WHERE Room_id = ? AND Property_id = ? AND
        // ( (Check_in <= ? AND Check_out > ?) OR 
        //   (Check_in < ? AND Check_out >= ?) OR 
        //   (Check_in >= ? AND Check_out <= ?) ) AND 
        // Booking_status = 'successful' AND Check_status != 'Cancelled'");
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM booking 
        WHERE Room_id = ? AND Property_id = ? AND
        ( (Check_in <= ? AND Check_out > ?) OR 
          (Check_in < ? AND Check_out >= ?) OR 
          (Check_in >= ? AND Check_out <= ?) )");
        $stmt->execute([$room_id, $property_id, $check_in, $check_in, $check_out, $check_out, $check_in, $check_out]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            return "ห้องพักไม่ว่างในช่วงวันที่เลือก";
        }
        return true;
    }

    public function get_Refund_Book($email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?  ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->conn->prepare("SELECT b.User_id,p.Property_name,
        p.Property_province,p.Property_district,p.Property_subdistrict,p.Property_image
        ,b.Booking_id,h.Host_firstname,h.Host_lastname,h.Host_phone,b.Check_in,b.Check_out
        ,b.Guests,b.Night,b.Booking_status,b.Total_price
        ,b.Payment_status,b.Create_at,b.Check_status,rf.Refund_status,rf.Host_check FROM booking b 
        INNER JOIN property p ON b.Property_id = p.Property_id 
        INNER JOIN host h ON p.Host_id = h.Host_id
        INNER JOIN refund rf ON rf.Host_id = h.Host_id
        WHERE b.User_id = ? && b.Booking_status= 'failed' && Payment_status = 'paid' && rf.Refund_status='approve'
        ORDER BY b.Create_at DESC");
        $stmt->execute([$user['User_id']]);
        $refund_booking = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $refund_booking;
    }

    public function get_pending($property_id)
    {
        $sql = $sql = "SELECT 'Online' AS Source,
               b.Booking_id,b.Property_id,p.Property_name,b.Room_id,
               u.Firstname,u.Lastname,u.Phone,b.Guests,b.Check_in AS CheckIn,
               b.Check_out ,b.Night,b.Total_price,b.Payment_status,
               b.Create_at,b.Check_status
        FROM booking b
        INNER JOIN user u ON b.User_id = u.User_id 
        INNER JOIN property p ON b.Property_id = p.Property_id
        WHERE b.Property_id = ? AND b.Check_status = 'Pending'
        UNION ALL 
        SELECT 'Walkin' AS Source,w.WalkIn_id AS Booking_id,w.Property_id,
               p.Property_name,w.Room_id,w.Firstname,w.Lastname,w.Phone,
               w.Guests,w.Check_in AS CheckIn,w.Check_out ,w.Night,
               w.Total_price,w.Payment_status,w.Create_at,w.Check_status
        FROM walkin w
        INNER JOIN property p ON w.Property_id = p.Property_id
        WHERE w.Property_id = ? AND w.Check_status = 'Pending'
        ORDER BY CheckIn";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id, $property_id]);
        $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $pending;
    }
    public function get_check_in($property_id)
    {
        $sql = "SELECT 'Online' AS Source,b.Booking_id,b.Property_id,p.Property_name,b.Room_id,u.Firstname,
               u.Lastname, u.Phone,b.Guests,b.Check_in AS CheckIn,b.Check_out ,b.Night,
               b.Total_price,b.Payment_status,b.Create_at,b.Check_status
        FROM booking b
        INNER JOIN user u ON b.User_id = u.User_id 
        INNER JOIN property p ON b.Property_id = p.Property_id
        WHERE b.Property_id = ? AND b.Check_status = 'Checked_in'
        UNION ALL 
        SELECT 'Walkin' AS Source,w.WalkIn_id AS Booking_id,w.Property_id,p.Property_name,
               w.Room_id,w.Firstname,w.Lastname,w.Phone,w.Guests,w.Check_in AS CheckIn,
               w.Check_out,w.Night,w.Total_price,w.Payment_status,w.Create_at,w.Check_status
        FROM walkin w
        INNER JOIN property p ON w.Property_id = p.Property_id
        WHERE w.Property_id = ? AND w.Check_status = 'Checked_in'
        ORDER BY CheckIn ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id, $property_id]);
        $check_in = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $check_in;
    }
    public function get_check_out($property_id)
    {
        $sql = "SELECT 'Online' AS Source,b.Booking_id,b.Property_id,p.Property_name,
               b.Room_id,u.Firstname,u.Lastname,u.Phone,b.Guests,b.Check_in AS CheckIn,
               b.Check_out ,b.Night,b.Total_price,b.Payment_status,
               b.Create_at,b.Check_status
        FROM booking b
        INNER JOIN user u ON b.User_id = u.User_id 
        INNER JOIN property p ON b.Property_id = p.Property_id
        WHERE b.Property_id = ? AND b.Check_status = 'Checked_out'
        UNION ALL 
        SELECT 'Walkin' AS Source,w.WalkIn_id AS Booking_id,w.Property_id,
               p.Property_name,w.Room_id,w.Firstname,w.Lastname,w.Phone,
               w.Guests,w.Check_in AS CheckIn,w.Check_out ,w.Night,w.Total_price,
               w.Payment_status,w.Create_at,w.Check_status
        FROM walkin w
        INNER JOIN property p ON w.Property_id = p.Property_id
        WHERE w.Property_id = ? AND w.Check_status = 'Checked_out'
        ORDER BY CheckIn";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id, $property_id]);
        $check_out = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $check_out;
    }
    public function notify($property_id)
    {

        $sql = "SELECT COUNT(*) as total
FROM (
    SELECT b.Check_in
    FROM booking b
    WHERE b.Property_id = ?
      AND DATE(b.Check_in) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    UNION ALL
    SELECT w.Check_in
    FROM walkin w
    WHERE w.Property_id = ?
      AND DATE(w.Check_in) BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
) AS upcoming
    ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id, $property_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    public function cancel_booking($booking_id)
    {
        if (!$booking_id) {
            return "ไม่มีข้อมูลการจอง";
        }
        $updateSQL = "UPDATE booking SET Booking_status = 'failed', Check_status = 'Cancelled' WHERE Booking_id = ?";
        $stmt = $this->conn->prepare($updateSQL);
        try {
            $stmt->execute([$booking_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการยกเลิกการจอง: " . $e->getMessage();
        }
    }
}
