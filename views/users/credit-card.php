<?php
session_start();
if (!isset($_SESSION["User_email"])) {
    header("Location: user-login.php");
    exit();
}
require_once __DIR__ . '/../../api/get_listBook.php';
// require_once __DIR__ . '/../controls/check_status.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <script src="https://cdn.omise.co/omise.js"></script>
    <style>
        body {
            font-family: 'IBM Plex Sans Thai', -apple-system, BlinkMacSystemFont, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            display: flex;
            max-width: 900px;
            width: 100%;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            overflow: hidden;
            margin: 20px;
        }

        .left-side {
            flex: 1;
            background: url('https://images.unsplash.com/photo-1641339914610-f7e4a1610f1b') no-repeat center center;
            background-size: cover;
            min-height: 400px;
        }

        .right-side {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h1 {
            font-size: 24px;
            color: #1a1a1a;
            margin-bottom: 24px;
            text-align: center;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            background: #fafafa;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
        }

        .form-row {
            display: flex;
            gap: 16px;
        }

        .form-row .form-group {
            flex: 1;
        }

        button {
            background-color: #2563eb;
            color: #fff;
            padding: 14px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover:not(.is-disabled) {
            background-color: #1e40af;
        }

        button.is-disabled {
            opacity: 0.6;
            pointer-events: none;
        }

        .msg {
            margin-top: 20px;
            text-align: center;
            font-size: 14px;
            color: #333;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                margin: 10px;
            }

            .left-side {
                min-height: 200px;
            }

            .right-side {
                padding: 24px;
            }
        }

        .btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn.loading::after {
            content: "";
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="left-side"></div>
        <div class="right-side">
            <h1>ชำระเงินด้วยบัตร</h1>
            <form id="card-form">
                <div class="form-group">
                    <label for="card-name">ชื่อบนบัตร</label>
                    <input type="text" id="card-name" autocomplete="cc-name" placeholder="ชื่อ-นามสกุล" required>
                </div>
                <div class="form-group">
                    <label for="card-number">เลขบัตร</label>
                    <input type="text" id="card-number" inputmode="numeric" max="16" autocomplete="cc-number"
                        placeholder="4242 4242 4242 4242" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="card-exp-month">เดือนหมดอายุ</label>
                        <input type="number" id="card-exp-month" min="1" max="12" placeholder="MM" required>
                    </div>
                    <div class="form-group">
                        <label for="card-exp-year">ปีหมดอายุ</label>
                        <input type="number" id="card-exp-year" min="2025" max="2040" placeholder="YYYY" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="card-cvv">CVV</label>
                    <input type="password" id="card-cvv" max="3" inputmode="numeric" autocomplete="cc-csc"
                        placeholder="123" required>
                </div>
                <div class="form-group">
                    <label for="amount-baht">จำนวนเงิน (บาท)</label>
                    <input type="number" id="amount-baht" value="<?php echo htmlspecialchars($total_price) ?>"
                        step="0.01" min="1" disabled>
                </div>
                <button id="pay-btn" type="submit">ชำระเงิน</button>
                <button id="cancel-btn">ยกเลิก</button>
                <div class="msg" id="msg"></div>
            </form>
        </div>
    </div>
    <script>
        const msgEl = document.getElementById('msg');
        const form = document.getElementById('card-form');
        const payBtn = document.getElementById('pay-btn');
        const cancelBtn = document.getElementById('cancel-btn');

        function setLoading(isLoading) {
            payBtn.classList.toggle('is-disabled', isLoading);
            payBtn.classList.add('loading');
            payBtn.textContent = isLoading ? 'กำลังดำเนินการ...' :
                'ยกเลิกชำระเงิน...';
        }
        // Format card number with spaces
        document.getElementById('card-number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            e.target.value = formatted.trim();
        });
        cancelBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            setLoading(false);
            msgEl.textContent = 'Payment process cancelled.';
            fetch('../../controls/check_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        booking_id: "<?php echo (int)$booking_id ?>",
                    })

                })
                .then(response => {
                    if (!response.ok) throw new Error('network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.href = "main-menu.php";
                        console.log(data.message);
                    }
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        })
        payBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            setLoading(true);
            msgEl.textContent = '';
            const amount = "<?php echo (int)$total_price ?>";
            const booking_id = "<?php echo (int)$booking_id ?>";
            const name = document.getElementById('card-name').value.trim();
            const number = document.getElementById('card-number').value.replace(/\s+/g, '');
            const expMonth = parseInt(document.getElementById('card-exp-month').value, 10);
            const expYear = parseInt(document.getElementById('card-exp-year').value, 10);
            const cvv = document.getElementById('card-cvv').value.trim();
            const amountBaht = parseFloat(amount);
            console.log("amount:", amount, "booking_id:", booking_id, number, expMonth, expYear, );
            if (amount && booking_id) {
                if (!name || !number || !expMonth || !expYear || !cvv || !amountBaht) {
                    msgEl.textContent = 'กรุณากรอกข้อมูลให้ครบ';
                    setLoading(false);
                    return;
                }
            }
            console.log("ข้อมูลครบแล้ว ทำการจองต่อ...");

            try {
                const satang = Math.round(amountBaht * 100);
                alert('Sending payment information...');
                fetch('../../controls/check_credit-cards.php', {
                        method: 'POST',
                        credentials: 'include',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            booking_id: booking_id,
                            Username: name,
                            number_card: number,
                            expMonth: expMonth,
                            expYear: expYear,
                            cvv: cvv,
                            amount: satang,

                        })
                    })
                    .then(response => {
                        if (!response.ok) throw new Error('network response was not ok');
                        return response.json();
                    })

                    .then(data => {
                        if (data.success == true) {
                            alert(data.message);
                            window.location.href = "../../controls/send_email.php";
                        } else {
                            console.error('เกิดข้อผิดพลาดในการบันทึกสถานะการชำระเงิน' + data.message);
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
                                        window.location.href = "main-menu.php";
                                        console.log(data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                });
                        }
                    })
                    .catch(err => {
                        console.error('เกิดข้อผิดพลาด' + err.message);

                    });
            } catch (error) {
                console.error('Error:', error);
            } finally {
                setLoading(false);
            }
        });
    </script>
</body>

</html>