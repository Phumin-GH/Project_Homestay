<?php
session_start();
if (!isset($_SESSION['User_email'])) {
    header("Location: user-login.php");
    exit();
}
require_once __DIR__ . '/../../api/get_listBook.php';
// var_dump($qrcode);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
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

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--card-background);
            border-radius: 8px;
            border: 1px dashed var(--border-color);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 2rem;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <header>
        <nav class="Navbar">
            <div class="Logo">
                <h1>
                    <img src="../../public/images/logo.png" alt="Logo" class="logo-image"
                        style="width: 3.5rem; height: 3.5rem;">
                    Homestay bookings
                </h1>
            </div>
        </nav>
    </header>
    <div class="Layout">
        <?php if ($errorMessage): ?>
            <div class="empty-state">
                <i class="fa-solid fa-shop-slash"></i>
                <h3>ไม่สามารถสร้าง QR Code ได้</h3>
                <p><strong>สาเหตุ:</strong> <?php echo htmlspecialchars($errorMessage); ?></p>
                <a href="main-menu.php">กลับสู่หน้าหลัก</a>
            </div>
        <?php else: ?>
            <div class="Main-content">
                <div class="Profile-container">
                    <div class="Profile-info">
                        <div class="Info-section">
                            <h3>หน้าการชำระเงิน</h3>
                            <h4>แสกน QR Code เพื่อชำระเงิน</h4>
                            <div class="qr-container">
                                <img src="<?php echo $qrcode['source']['scannable_code']['image']['download_uri']; ?>"
                                    alt="" class="qrcode">
                            </div>
                            <div class="payment-info-grid">
                                <div>
                                    <p>รหัสการชำระเงิน : <?php echo $qrcode['id'] ?></p>
                                    <p>จำนวนเงิน : <?php echo number_format($qrcode['amount'] / 100, 2); ?> บาท</p>
                                    <p>สถานะ: <span
                                            id="payment-status"><?php echo isset($qrcode['status']) ? $qrcode['status'] : 'รอการชำระเงิน'; ?></span>
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
        const expiresAt = <?php echo $expires_at_timestamp; ?>;
        const chargeId = "<?php echo addslashes($qrcode['id']); ?>";
        const qrCode = "<?php echo addslashes($qrcode['source']['scannable_code']['image']['download_uri']); ?>";
        // const booking_id = "<?php /*echo addslashes($qrcode['booking_Id'])*/ ?>";
        const booking_id = "<?php echo htmlspecialchars($booking_id) ?>";

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

        // ฟังก์ชันเช็คสถานะ
        function checkChargeStatus() {
            fetch(`../../controls/check_status.php`, {
                    method: 'POST',
                    // credentials: 'include',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        charge_id: chargeId,
                        booking_id: booking_id,
                        qrCode: qrCode
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success === true) {
                        alert(data.message);
                        document.getElementById('payment-status').innerText = "Succesful";
                        clearInterval(statusInterval);
                        clearInterval(countdownInterval);
                        window.location.href = "../../controls/send_email.php";
                        console.log(data.message);
                    } else {
                        // alert(data.message);
                        fetch('../../controls/check_status.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    booking_id: booking_id,
                                })

                            })
                            .then(response => {
                                if (!response.ok) throw new Error('network response was not ok');
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    clearInterval(statusInterval);
                                    clearInterval(countdownInterval);
                                    window.location.href = "main-menu.php";
                                    console.log(data.message);
                                } else {}
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error
                        .message);
                    // document.getElementById('countdown').innerText = 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ: ' +
                    //     error
                    //     .message;

                });
        }
        // เรียกนับถอยหลัง
        updateCountdown();
        checkChargeStatus();
        const countdownInterval = setInterval(updateCountdown, 1000);
        // เรียกเช็คสถานะทุก 5 วินาที
        const statusInterval = setInterval(checkChargeStatus, 5000);
    </script>
<?php
        endif;
?>
</body>

</html>