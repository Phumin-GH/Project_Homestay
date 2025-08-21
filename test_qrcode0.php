<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

define('OMISE_PUBLIC_KEY', 'pkey_test_64nbbhnxh0371dz2kzi');
define('OMISE_SECRET_KEY', 'skey_test_64nbbhodcchurub65uw');

$total_price = isset($_GET['total_price']) ? (float)$_GET['total_price'] : 0;

if ($total_price <= 0) {
    die("ราคาที่ส่งมาไม่ถูกต้อง");
}

// ถ้ามี charge_id เดิม → ตรวจสอบสถานะ
if (isset($_SESSION['charge_id'])) {
    $oldCharge = OmiseCharge::retrieve($_SESSION['charge_id']);
    if ($oldCharge['status'] === 'pending') {
        $charge = $oldCharge;
    } else {
        unset($_SESSION['charge_id']); // ลบถ้าไม่ pending
    }
}

// ถ้าไม่มี หรือหมดอายุแล้ว → สร้างใหม่
if (!isset($charge)) {
    $charge = OmiseCharge::create([
        'amount' => $total_price * 100,
        'currency' => 'thb',
        'source' => ['type' => 'promptpay'],
        'return_uri' => 'https://example.com/thankyou.php',
    ]);
    $_SESSION['charge_id'] = $charge['id'];
}

// แปลง expires_at ของ Omise เป็น timestamp สำหรับ JS
$expires_at_timestamp = strtotime($charge['expires_at']) * 1000;
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ชำระเงินด้วย Omise PromptPay</title>
</head>

<body>
    <h2>ชำระเงิน</h2>

    <!-- แสดง QR Code -->
    <img src="<?php echo $charge['source']['scannable_code']['image']['download_uri']; ?>"
        style="width: 25rem; height: 25rem;" />

    <p>Charge ID: <?php echo $charge['id']; ?></p>
    <p>จำนวนเงิน: <?php echo number_format($charge['amount'] / 100, 2); ?> บาท</p>
    <p>สถานะ: <span id="payment-status"><?php echo $charge['status']; ?></span></p>
    <p>สร้างเมื่อ: <?php echo date('Y-m-d H:i:s', strtotime($charge['created_at'])); ?></p>
    <p>หมดอายุ: <?php echo $charge['expires_at']; ?></p>

    <div>เวลาที่เหลือในการชำระเงิน: <span id="countdown">กำลังโหลด...</span></div>

    <script>
    const expiresAt = <?php echo $expires_at_timestamp; ?>;
    const chargeId = "<?php echo $charge['id']; ?>";

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiresAt - now;

        if (distance < 0) {
            document.getElementById("countdown").innerHTML = "หมดเวลาชำระเงิน";
            clearInterval(countdownInterval);
            clearInterval(statusInterval);
            return;
        }

        const hours = Math.floor((distance / (1000 * 60 * 60)));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdown").innerHTML = hours + " ชั่วโมง " +
            minutes + " นาที " + seconds + " วินาที";
    }

    updateCountdown();
    const countdownInterval = setInterval(updateCountdown, 1000);

    function checkChargeStatus() {
        fetch(`check_status.php?charge_id=${chargeId}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('payment-status').innerText = data.status;

                if (data.status === 'successful' || data.paid) {
                    alert('ชำระเงินสำเร็จ!');
                    clearInterval(statusInterval);
                    clearInterval(countdownInterval);
                    window.location.href = 'users/main-menu.php';
                } else if (data.status === 'expired') {
                    alert('หมดเวลาชำระเงิน');
                    clearInterval(statusInterval);
                    clearInterval(countdownInterval);
                }
            });
    }

    const statusInterval = setInterval(checkChargeStatus, 5000);
    checkChargeStatus();
    </script>
</body>

</html>