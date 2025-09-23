<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Booking.php';
header('Content-Type: application/json');
$BookingHandler = new Booking($conn);
// $price = (int)($_POST['price'] ?? 0);
try {

    if (isset($_POST['submit_onl'])) {
        if (isset($_POST['total_price']) && isset($_POST['booking_id']) && isset($_POST['method'])) {
            $_SESSION['total_price'] = $_POST['total_price'] ?? null;
            $_SESSION['booking_id'] = $_POST['booking_id'] ?? null;
            $_SESSION['method'] = $_POST['method'] ?? null;
            $method = $_SESSION['method'];
            $result = $BookingHandler->TypeGateway($method);
            if ($result === false) {
                echo json_encode(["gateway" => "unknow", "err" => "Unknow Method"]);
                exit();
            } else {
                echo json_encode(["gateway" => $result, "msg" => $result]);
                exit();
            }
        } elseif (isset($_POST['submit_onl'])) {
            $email = $_SESSION['User_email'];
            $room_id = (int)($_POST['room_id'] ?? 0);
            $property_id = (int)($_POST['property_id'] ?? 0);
            $check_in_date = $_POST['check_in_date'];
            $check_out_date = $_POST['check_out_date'];
            $nights = (int)($_POST['nights'] ?? 0);
            $total_price = (float)($_POST['total_price']) ?? 0;
            $guests = (int)($_POST['guests'] ?? 0);
            $vat = (float)($_POST['vat_values'] ?? 0);
            $service = (int)($_POST['service_values'] ?? 0);
            if (isset($_SESSION['User_email'])) {
                $result = $BookingHandler->book_online($email, $property_id, $room_id, $check_in_date, $check_out_date,  $nights, $guests, $total_price, $service, $vat);
                if (is_numeric($result)) {
                    $booking_id = $result;
                    echo json_encode(
                        [
                            'success' => true,
                            'booking_id' => $booking_id,
                            'total_price' => round($total_price, 2),
                            'message' => 'จองสำเร็จ'
                        ]
                    );
                    exit;
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => $result
                    ]);
                    exit;
                }
            }
        }
    } elseif (isset($_POST['submit_wki'])) {
        $room_id = (int)($_POST['room_id'] ?? 0);
        $property_id = (int)($_POST['property_id'] ?? 0);
        $check_in_date = $_POST['check_in_date'];
        $check_out_date = $_POST['check_out_date'];
        $f_name = $_POST['firstName'];
        $l_name = $_POST['lastName'];
        $phone = $_POST['guestsPhone'];
        $nights = (int)($_POST['nights'] ?? 0);
        $total_price = (int)($_POST['total_price']) ?? 0;
        $guests = (int)($_POST['guests'] ?? 0);
        $result = $BookingHandler->book_walkin($property_id, $room_id, $check_in_date, $check_out_date, $f_name, $l_name, $phone, $nights, $guests, $total_price);
        if ($result === true) {
            echo json_encode([
                'success' => true,
                'message' => "สำเร็จ"

            ]);
            exit;
        } else {
            echo json_encode([
                'success' => false,
                'message' => "ไม่สำเร็จ"

            ]);
            exit;
        }
    } elseif (isset($_POST['cancel_btn'])) {
        if (isset($_POST['booking_id'])) {
            $booking_id = (int)($_POST['booking_id'] ?? 0);
            if (!$booking_id) {
                echo json_encode(['success' => false, 'message' => 'รหัสการจองไม่ถูกต้อง']);
                exit();
            }
            $result = $BookingHandler->cancel_booking($booking_id);
            if ($result === true) {
                echo json_encode(['success' => true, 'message' => 'ยกเลิกการจองเรียบร้อยแล้ว']);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'one price']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่มีรหัสการจอง']);
            exit();
        }
    } else {
        $room_id = (int)($_POST['room_id'] ?? 0);
        $property_id = (int)($_POST['property_id'] ?? 0);
        $nights = (int)($_POST['nights'] ?? 0);
        $guests = (int)($_POST['guests'] ?? 0);
        $check_in = (int)($_POST['check_in_date'] ?? 0);
        $check_out = (int)($_POST['check_out_date'] ?? 0);
        // $price = (int)($_POST['price'] ?? 0);
        $check_date = $BookingHandler->check_Calender($property_id, $room_id, $check_in, $check_out);
        if (is_string($check_date)) {
            echo json_encode([
                'check_success' => false,
                'check_msg' => $check_date
            ]);
            exit();
        } else {
            $prices = $BookingHandler->calcuratePrice($room_id, $nights, $guests);
            if (is_numeric($prices)) {
                $service = 0.1;
                $vat_tax = 0.07;
                $service_amount = $prices * $service;
                $tax_amount = $service_amount * $vat_tax;
                $total_price = $prices + $tax_amount + $service_amount;
                echo json_encode([
                    'success' => true,

                    'total_price' => round($total_price, 2),
                    'service' => round($service_amount, 2),
                    'vat' => round($tax_amount, 2)
                ]);
                exit();
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => $prices
                ]);
                exit();
            }
        }
    }

    if ($_POST['charge_id']) {
        $charge_id = $_POST['charge_id'] ?? 0;
        if (isset($charge_id)) {
            $payment_status = $_POST['payment_status'] ?? '';
            $qrcode = $_POST['qrCode'] ?? '';
            $booking_id = $_POST['booking_id'] ?? 0;
            $booking_status = $_POST['booking_status'] ?? '';
            $payment_status = $_POST['payment_status'] ?? '';
            $result = $BookingHandler->PaymentStatus($charge_id, $payment_status, $qrcode, $booking_id, $booking_status);
            echo json_encode($result);
            exit();
        }
    } elseif ($_GET['charge_id']) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // path ไป root ของโปรเจกต์
        $dotenv->load();

        define('OMISE_PUBLIC_KEY', $_ENV['OMISE_PUBLIC_KEY']);
        define('OMISE_SECRET_KEY', $_ENV['OMISE_SECRET_KEY']);
        $charge_id = $_GET['charge_id'] ?? '';
        if (!$charge_id) {

            echo json_encode(['error' => 'No charge_id provided']);
            exit;
        } else {
            try {

                $charge = OmiseCharge::retrieve($charge_id);
                echo json_encode([
                    'status' => $charge['status'],
                    'paid' => $charge['paid'],
                    'expired' => $charge['expired'] ?? false,
                    'authorized' => $charge['authorized'] ?? false,
                ]);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดร้ายแรงในฝั่งเซิร์ฟเวอร์: ' . $e->getMessage()

    ]);
    exit();
}
