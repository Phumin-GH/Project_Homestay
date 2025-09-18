<?php
class Payment
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล

    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function pushBooking($email,  $booking_id)
    {

        $usersql = "SELECT User_id FROM User WHERE User_email = ?";
        $stmt = $this->conn->prepare($usersql);
        $stmt->execute([$email]);
        $user_row = $stmt->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบว่าหา user เจอหรือไม่
        if (!$user_row) {
            return "ไม่พบผู้ใช้งานสำหรับอีเมล";
        }
        $user_id = $user_row['User_id']; // ดึงค่า id ออกมา

        // --- [FIX 2] แก้ไข SQL ให้เฉพาะเจาะจงโดยใช้ booking_id ---
        $sql = "SELECT b.Booking_id,b.Charge_id,h.Host_firstname,h.Host_phone,u.User_email,u.Firstname,u.Lastname,p.Property_name,
        p.Property_province,p.Property_district,p.Property_subdistrict,r.Room_number,r.Room_capacity,b.Guests,
        b.Check_in,b.Check_out,b.Total_price,b.Create_at
        FROM Booking b 
        INNER JOIN Property p ON b.Property_id = p.Property_id
        INNER JOIN User u ON b.User_id = u.User_id
        INNER JOIN Room r ON b.Room_id = r.Room_id
        INNER JOIN Host h ON p.Host_id = h.Host_id
        WHERE b.Booking_id = ? AND b.User_id = ?"; // ระบุ Booking_id และ User_id

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id, $user_id]); // ส่งค่า booking_id และ user_id
        $list_book = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($list_book) {
            $_SESSION['email_data'] = [
                'booking_id' => $list_book['Booking_id'],
                'host_firstname' => $list_book['Host_firstname'],
                'host_phone' => $list_book['Host_phone'],
                'user_firstname' => $list_book['Firstname'],
                'user_lastname' => $list_book['Lastname'],
                'user_email' => $list_book['User_email'],
                'property_name' => $list_book['Property_name'],
                'property_pro' => $list_book['Property_province'],
                'property_dis' => $list_book['Property_district'],
                'property_sub' => $list_book['Property_subdistrict'],
                'room_num' => $list_book['Room_number'],
                'room_cap' => $list_book['Room_capacity'],
                'checkIn' => $list_book['Check_in'],
                'checkOut' => $list_book['Check_out'],
                'guests' => $list_book['Guests'],
                'total_price' => $list_book['Total_price'],
                'bookingDate' => $list_book['Create_at'],
            ];
            return true;
        } else {
            // ถ้าหาไม่เจอ ให้หยุดทำงานและแจ้งข้อผิดพลาด
            return "ไม่พบข้อมูลการจองสำหรับ";
        }
    }
    public function Omise_API()
    {
        require_once __DIR__ . '/../../vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../'); // path ไป root ของโปรเจกต์
        $dotenv->load();
        if (!defined('OMISE_PUBLIC_KEY')) {
            define('OMISE_PUBLIC_KEY', $_ENV['OMISE_PUBLIC_KEY']);
        }
        if (!defined('OMISE_SECRET_KEY')) {
            define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);
        }
        define('OMISE_API_VERSION', '2019-05-29');
    }
    public function insertCreditCard($booking_id, $name, $number, $exMonth, $exYear, $cvv, $amount)
    {
        if (empty($booking_id) || empty($name) || empty($number) || empty($exMonth) || empty($exYear) || empty($cvv) || empty($amount)) {
            return "ข้อมูลสำหรับการชำระเงินไม่ครบถ้วน";
        }
        try {
            $token = OmiseToken::create([
                'card' => [
                    'name' => $name,
                    'number' => $number,
                    'expiration_month' => $exMonth,
                    'expiration_year' => $exYear,
                    'security_code' => $cvv
                ]
            ]);
            $charge = OmiseCharge::create([
                'amount' => $amount,
                'currency' => 'thb',
                'card' => $token['id'],
                'description' => 'Test Payment via '
            ]);
            if ($charge['status'] === 'successful') {
                // Save the booking information to the database
                $number_card = null;
                if ($number == '4242424242424242') {
                    $number_card = 'Visa';
                } elseif ($number == '5555555555554444') {
                    $number_card = 'MasterCard';
                }
                $sql = "UPDATE booking SET Payment_status = ?, Payment_gateway = ?, Booking_status = ?, Charge_id = ? WHERE Booking_id = ?";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute(['paid', $number_card, 'successful', $charge['id'], $booking_id]);
                return true;
                // echo json_encode(["success" => true, "message" => "✅ Payment successful! " . $charge['status'], $charge['id']]);
            } else {
                return "Payment failed";
                // echo json_encode(["success" => false, "message" => "❌ Payment failed. Status: " . $charge['status']]);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function generateQRcode($total_price, $booking_id, $method)
    {
        $this->Omise_API();
        if ($total_price <= 0 || $booking_id <= 0 || empty($method)) { // แก้ไขเงื่อนไขเล็กน้อย
            return "ข้อมูลสำหรับการชำระเงินไม่ครบถ้วน";
        }
        try {
            if ($method === 'qrcode') {
                $total_amount = $total_price * 100; // Omise ต้องการหน่วยสตางค์
                $charge = OmiseCharge::create([
                    'amount' => $total_amount,
                    'currency' => 'thb',
                    'source' => [
                        'type' => 'promptpay'
                    ],
                    'return_uri' => 'https://example.com/thankyou.php',
                    'description' => 'Test Payment QR Code',
                    // 'booking_Id' => $booking_id
                ]);
                // ส่วนนี้คือการแสดงผล QR Code ให้ผู้ใช้ (ตัวอย่าง)
                // header('Content-Type: application/json');
                // echo json_encode(['qr_code_url' => $charge['source']['scannable_code']['image']['download_uri'], 'expires_at' => $expires_at_timestamp]);
                return $charge;
            } else {
                return "Method not supported";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }


    public function CheckStatus($charge_id, $booking_id, $qrCode)
    {
        $this->Omise_API();
        if (empty($charge_id)) {
            return 'No charge_id provided';
        }
        try {
            $charge = OmiseCharge::retrieve($charge_id);
            $payment_st =  [
                'success'    => true,
                'status'     => $charge['status'],
                'paid'       => $charge['paid'],
                'expired'    => $charge['expired'] ?? false,
                'authorized' => $charge['authorized'] ?? false,

            ];

            if ($payment_st['success'] === true && $payment_st['paid'] === true && $payment_st['expired'] === false) {
                $status = $this->update_payment_status($charge_id, $booking_id, $qrCode);
                return true;
            }
            // else {
            //     $status = "ชำระเงินไม่สำเร็จ. สถานะ: " . $payment_st['status'];
            // }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update_payment_status($charge_id, $booking_id, $qrCode)
    {
        // if (!$charge_id  || !$qrCode || !$booking_id) {
        //     return "ไม่มีข้อมูลการจอง";
        // }
        // SQL บันทึกข้อมูล
        $sql = "UPDATE booking SET Charge_id = ?,Booking_status = 'successful',Payment_gateway = 'Qrcode',
    Payment_status = 'paid',Booking_qrcode = ? WHERE Booking_id = ?";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                $charge_id,
                $qrCode,
                $booking_id,
            ]);
            return true;
        } catch (PDOException $e) {
            return $e->getMessage();
        }
    }
    // public function update_payment_status($charge_id, $payment_status, $qrcode, $booking_id, $booking_status)
    // {
    //     if (!$charge_id || !$payment_status || !$qrcode || !$booking_id || !$booking_status) {
    //         return "ไม่มีข้อมูลการจอง";
    //     }
    //     // SQL บันทึกข้อมูล
    //     $sql = "UPDATE booking SET Charge_id = ?,Booking_status = ?,Payment_gateway = 'Qrcode',
    // Payment_status = ?,Booking_qrcode = ? WHERE Booking_id = ?";
    //     try {
    //         $stmt = $this->conn->prepare($sql);
    //         $stmt->execute([
    //             $charge_id,
    //             $booking_status,
    //             $payment_status,
    //             $qrcode,
    //             $booking_id,
    //         ]);
    //         return true;
    //     } catch (PDOException $e) {
    //         return $e->getMessage();
    //     }
    // }
}
