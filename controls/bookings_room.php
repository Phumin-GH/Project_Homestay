<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Booking.php';
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
            $total_price = (int)($_POST['total_price']) ?? 0;
            $guests = (int)($_POST['guests'] ?? 0);
            if (isset($_SESSION['User_email'])) {
                $result = $BookingHandler->book_online($email, $property_id, $room_id, $check_in_date, $check_out_date,  $nights, $guests, $total_price);
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
                        'message' => 'จองไม่สำเร็จ'
                    ]);
                    exit;
                }
            }
            // if (isset($_SESSION['method'])) {
            //     if ($method === 'credit-card') {
            //         echo "credit-card";
            //     } else if ($method === 'qrcode') {
            //         echo "qrcode";
            //     } else {
            //         echo "no_price";
            //     }
            // }
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
    } else {
        $room_id = (int)($_POST['room_id'] ?? 0);
        $nights = (int)($_POST['nights'] ?? 0);
        $guests = (int)($_POST['guests'] ?? 0);
        // $price = (int)($_POST['price'] ?? 0);
        $prices = $BookingHandler->calcuratePrice($room_id, $nights, $guests);
        if (is_numeric($prices)) {
            echo json_encode([
                'success' => true,
                'total_price' => round($prices, 2)
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
