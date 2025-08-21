<?php
require_once __DIR__ . '/../vendor/autoload.php';
session_start();
if (!isset($_SESSION['User_email'])) {
    header("Location: user-login.php");
    exit();
}
require_once __DIR__ . '/../config/db_connect.php';

define('OMISE_PUBLIC_KEY','pkey_test_64nbbhnxh0371dz2kzi');
define('OMISE_SECRET_KEY', 'skey_test_64nbbhodcchurub65uw');
$email = $_SESSION['User_email'];
$total_price = $_SESSION['total_price'] ?? 0;
$booking_id = $_SESSION['booking_id'] ?? 0;
$method = $_SESSION['method'] ?? 0;

// --- [FIX 1] ดึงค่า User ID ออกมาจาก Array ก่อน ---
$usersql = "SELECT User_id FROM User WHERE User_email = ?";
$stmt = $conn->prepare($usersql);
$stmt->execute([$email]);
$user_row = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่าหา user เจอหรือไม่
if (!$user_row) {
    die("ไม่พบผู้ใช้งานสำหรับอีเมล: " . htmlspecialchars($email));
}
$user_id = $user_row['User_id']; // ดึงค่า id ออกมา

// --- [FIX 2] แก้ไข SQL ให้เฉพาะเจาะจงโดยใช้ booking_id ---
$sql = "SELECT b.Charge_id,h.Host_firstname,h.Host_phone,u.User_email,u.Firstname,u.Lastname,p.Property_name,
p.Property_province,p.Property_district,p.Property_subdistrict,r.Room_number,r.Room_capacity,b.Guests,
b.Check_in,b.Check_out,b.Total_price,b.Create_at
FROM Booking b 
INNER JOIN Property p ON b.Property_id = p.Property_id
INNER JOIN User u ON b.User_id = u.User_id
INNER JOIN Room r ON b.Room_id = r.Room_id
INNER JOIN Host h ON p.Host_id = h.Host_id
WHERE b.Booking_id = ? AND b.User_id = ?"; // ระบุ Booking_id และ User_id

$stmt = $conn->prepare($sql);
$stmt->execute([$booking_id, $user_id]); // ส่งค่า booking_id และ user_id
$list_book = $stmt->fetch(PDO::FETCH_ASSOC);

// --- [FIX 3] เพิ่มการตรวจสอบว่าหาข้อมูลเจอหรือไม่ ---
if ($list_book) {
    $_SESSION['email_data'] = [
        'charge_id' => $list_book['Charge_id'],
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
} else {
    // ถ้าหาไม่เจอ ให้หยุดทำงานและแจ้งข้อผิดพลาด
    die("ไม่พบข้อมูลการจองสำหรับ Booking ID: " . htmlspecialchars($booking_id));
}


if($total_price <=0 || $booking_id <= 0 || empty($method)){ // แก้ไขเงื่อนไขเล็กน้อย
    die("ข้อมูลสำหรับการชำระเงินไม่ครบถ้วน");
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
        ]);
        // set expire 5 นาที
        $expires_at = time() + 300;
        $expires_at_iso8601 = date('c', $expires_at);
        $expires_at_timestamp = strtotime($expires_at_iso8601) * 1000;
        
        // ส่วนนี้คือการแสดงผล QR Code ให้ผู้ใช้ (ตัวอย่าง)
        // header('Content-Type: application/json');
        // echo json_encode(['qr_code_url' => $charge['source']['scannable_code']['image']['download_uri'], 'expires_at' => $expires_at_timestamp]);

    } else {
        echo "Method not supported";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Homestay Booking</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    /* Header */
    header {
        background: #ffffff;
        border-bottom: 1px solid #e5e5e5;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100;
        height: 70px;
    }

    .Navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        max-width: 1400px;
        margin: 0 auto;
        height: 100%;
    }

    .Logo h1 {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .Logo-image {
        border-radius: 8px;
    }

    .Layout {
        display: flex;
        min-height: 85vh;
        margin-top: 70px;
        /* ให้ layout เริ่มหลังจาก navbar */
    }

    .Main-content {
        flex: 1;
        padding: 0.15rem;
        background: #fafafa;
        /* margin-left: 20rem; */
        /* ให้ main content เริ่มหลังจาก sidebar */
        /* min-height: calc(100vh); */
        transition: margin-left 0.3s ease;
    }

    .Profile-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
    }

    .Info-section h3 {
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .Info-section h3::before {
        content: "";
        width: 4px;
        height: 20px;
        background: #1e5470;
        border-radius: 2px;
    }

    .qr-container {
        display: flex;
        flex-direction: column;
        /* เรียงเป็นแนวตั้ง */
        align-items: center;
        /* จัดตรงกลางแนวนอน */
        justify-content: center;
        /* จัดตรงกลางแนวตั้ง */
        text-align: center;
        margin-top: 0.5rem;
    }

    .qrcode {
        width: 35rem;
        height: 35rem;
        margin-bottom: 0.5rem;
        /* เว้นระยะระหว่าง QR และ countdown */
    }

    .Profile-info {
        background: #ffffff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
    }

    .countdown-container {
        display: flex;
        flex-direction: column;
        /* เรียงเป็นแนวตั้ง */
        align-items: center;
        /* จัดตรงกลางแนวนอน */
        justify-content: center;
        /* จัดตรงกลางแนวตั้ง */
        text-align: center;
        margin-top: 2rem;
    }

    .countdown {
        color: #c30827ff;
        font-size: 20px;
        font-weight: bold;
    }

    .payment-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1.5rem;
        max-width: 1000px;
        border: 2px #1e5470 solid;
        border-radius: 15px;
        width: 100%;
    }

    .payment-info-grid div {
        background: #fff;
        border-radius: 10px;
        padding: 1rem;
        /* box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1); */
        font-size: 18px;
        border-radius: 25px;
    }

    .payment-info-grid p {
        margin-bottom: 0.5rem;
    }

    .Info-section h3 {
        color: #1e5470;
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .Info-section h4 {
        font-size: 1.1rem;
        font-weight: 500;
        color: #555;
        text-align: center;
        margin-bottom: 0.5rem;
    }
    </style>
</head>

<body>
    <header>
        <nav class="Navbar">
            <div class="Logo">
                <h1>
                    <img src="../images/logo.png" alt="Logo" class="logo-image" style="width: 3.5rem; height: 3.5rem;">
                    Homestay bookings
                </h1>
            </div>
        </nav>
    </header>
    <div class="Layout">
        <div class="Main-content">
            <div class="Profile-container">
                <div class="Profile-info">
                    <div class="Info-section">
                        <h3>หน้าการชำระเงิน</h3>
                        <h4>แสกน QR Code เพื่อชำระเงิน</h4>
                        <div class="qr-container">
                            <img src="<?php echo $charge['source']['scannable_code']['image']['download_uri']; ?>"
                                alt="" class="qrcode">
                        </div>
                        <div class="payment-info-grid">
                            <div>
                                <p>รหัสการชำระเงิน : <?php echo $charge['id']?></p>
                                <p>จำนวนเงิน : <?php echo number_format($charge['amount']/100,2);?> บาท</p>
                                <p>สถานะ: <span
                                        id="payment-status"><?php echo isset($charge['status']) ? $charge['status'] : 'รอการชำระเงิน'; ?></span>
                                </p>
                                <p>วันที่ : <?php echo date('Y-m-d'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="countdown-container">
                    <div class="countdown">
                        เวลาที่เหลือในการชำระเงิน:
                        <span id="countdown">กำลังโหลด...</span>
                    </div>
                    <div class="countdown">
                        <span id="error-message"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>

    <script>
    // ตัวแปรจาก PHP
    const countdownInterval = setInterval(updateCountdown, 1000);
    const expiresAt = <?php echo $expires_at_timestamp; ?>;
    const chargeId = "<?php echo addslashes($charge['id']); ?>";
    const qrCode = "<?php echo addslashes($charge['source']['scannable_code']['image']['download_uri']); ?>";
    const booking_id = "<?php echo addslashes($booking_id) ?>";
    // ฟังก์ชันนับถอยหลัง
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance < 0) {
            document.getElementById("countdown").innerHTML = "หมดเวลาชำระเงิน";
            document.getElementById("error-message").innerText = "การชำระเงินหมดอายุ";
            clearInterval(countdownInterval);
            clearInterval(statusInterval);
            window.location.href = 'main-menu.php';
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = hours + " ชั่วโมง " + minutes + " นาที " + seconds + " วินาที";
    }
    // เรียกนับถอยหลัง
    updateCountdown();
    // ฟังก์ชันเช็คสถานะ
    function checkChargeStatus() {
        fetch(`../controls/check_pm_status.php?charge_id=${chargeId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Charge status response:', data);
                if (data.error) {
                    document.getElementById('error-message').innerText = 'เกิดข้อผิดพลาด: ' + data.error;
                    return;
                }

                document.getElementById('payment-status').innerText = data.status;
                if (data.status === 'successful' || data.paid === true) {
                    let statusInt;
                    let bookingStatus;

                    switch (data.status) {
                        case 'successful':
                            statusInt = 1;
                            break;
                        default:
                            statusInt = 0; // pending
                    }
                    const statusPaid = data.paid ? 'Paid' : 'Unpaid';

                    fetch('../controls/payment_status.php', {
                            method: 'POST',
                            credentials: 'include',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                charge_id: chargeId,
                                booking_id: booking_id,
                                payment_status: statusPaid,
                                booking_status: statusInt,
                                qrCode: qrCode
                            })
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            console.log('Fetch response :', data);
                            if (data.success === true) {

                                alert('สถานะ' + data.message);
                                console.log(data.success);
                                clearInterval(statusInterval);
                                clearInterval(countdownInterval);
                                window.location.href = 'send_email.php';
                            } else {
                                alert('เกิดข้อผิดพลาดในการบันทึกสถานะการชำระเงิน: ');
                                console.error('Fetch error:', data.message);
                            }
                        })
                        .catch(err => {
                            alert('เกิดข้อผิดพลาด ขณะบันทึกสถานะการชำระเงิน');

                            console.error('Fetch error:', err);
                        });

                } else if (data.status === 'expired' || data.expired === true) {
                    document.getElementById('countdown').innerText = 'หมดเวลาชำระเงิน';
                    clearInterval(statusInterval);
                    clearInterval(countdownInterval);
                    window.location.href = 'main-menu.php';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('countdown').innerText = 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ: ' +
                    error
                    .message;

            });
    }

    // เรียกเช็คสถานะทุก 5 วินาที
    const statusInterval = setInterval(checkChargeStatus, 5000);
    checkChargeStatus();
    </script>
</body>

</html>