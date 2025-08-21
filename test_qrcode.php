<?php
require_once __DIR__ . '/vendor/autoload.php';

define('OMISE_PUBLIC_KEY', 'pkey_test_64nbbhnxh0371dz2kzi');
define('OMISE_SECRET_KEY', 'skey_test_64nbbhodcchurub65uw');
session_start();
$total_price = $_SESSION['total_price'] ?? 0;


if ($total_price <= 0) {
    die("ราคาที่ส่งมาไม่ถูกต้อง");
}

$amount_in_satang = $total_price * 100;


$charge = OmiseCharge::create([
    
    'amount' => $amount_in_satang,
    'currency' => 'thb',
    'source' => [
        'type' => 'promptpay'
    ],
    'return_uri' => 'https://example.com/thankyou.php',
    
]);

// กำหนดเวลาหมดอายุ เป็น 1 ชั่วโมงถัดไป (3600 วินาที)
$expires_at = time() + 60;
$expires_at_iso8601 = date('c', $expires_at);
// แปลงวันที่หมดอายุเป็น timestamp (มิลลิวินาที) สำหรับ JavaScript
$expires_at_timestamp = strtotime($expires_at_iso8601) * 1000;
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <title>ชำระเงินด้วย Omise PromptPay</title>
</head>

<body>
    <h2>ชำระเงิน</h2>

    <!-- แสดง QR Code -->
    <img src="<?php echo $charge['source']['scannable_code']['image']['download_uri']; ?>"
        style="width: 25rem; height: 25rem;" />
    <p>ID : <?php echo $charge['id']?></p>
    <p>จำนวนเงิน: <?php echo number_format($charge['amount'] / 100, 2); ?> บาท</p>
    <p>สถานะ: <span id="payment-status"><?php echo $charge['status']; ?></span></p>
    <p>สร้างเมื่อ: <?php echo date('Y-m-d H:i:s', $charge['created']); ?></p>

    <div>
        เวลาที่เหลือในการชำระเงิน: <span id="countdown">กำลังโหลด...</span>
    </div>

    <script>
    // ตัวแปรจาก PHP
    const expiresAt = <?php echo $expires_at_timestamp; ?>;
    const chargeId = "<?php echo $charge['id']; ?>";

    // ฟังก์ชันนับถอยหลัง
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance < 0) {
            document.getElementById("countdown").innerHTML = "หมดเวลาชำระเงิน";
            document.getElementById("error-message").innerText = "การชำระเงินหมดอายุ";
            clearInterval(countdownInterval);
            clearInterval(statusInterval);
            window.location.href = 'test_ajax.php';
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = hours + " ชั่วโมง " + minutes + " นาที " + seconds + " วินาที";
    }

    // เรียกนับถอยหลัง
    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);

    // ฟังก์ชันเช็คสถานะ
    function checkChargeStatus() {
        fetch(`check_status.php?charge_id=${chargeId}`)
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
                    alert('ชำระเงินสำเร็จ!');
                    clearInterval(statusInterval);
                    clearInterval(countdownInterval);
                    window.location.href = 'test_ajax.php';
                } else if (data.status === 'expired' || data.expired === true) {
                    document.getElementById('error-message').innerText = 'หมดเวลาชำระเงิน';
                    clearInterval(statusInterval);
                    clearInterval(countdownInterval);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                document.getElementById('error-message').innerText = 'เกิดข้อผิดพลาดในการตรวจสอบสถานะ: ' + error
                    .message;
                window.location.href = 'test_ajax.php';
            });
    }

    // เรียกเช็คสถานะทุก 5 วินาที
    const statusInterval = setInterval(checkChargeStatus, 5000);
    checkChargeStatus();
    </script>
</body>

</html>