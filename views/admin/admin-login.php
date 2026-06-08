<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/Loginstyle.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <div class="auth-container">
        <button class="close-button" id="close-auth">
            <i class="fas fa-times"></i>
        </button>
        <div class="auth-header">
            <div>
                <img src="../../public/images/logo.png" style="width: 5rem; height: 5rem;">
            </div>
            <h1>ยินดีต้อนรับแอดมินกลับมา</h1>
            <p>เข้าสู่ระบบบัญชีของคุณ</p>
        </div>
        <div class="auth-tabs">
            <div class="tab active" id="login-tab">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
            </div>
        </div>
        <div class="auth-content">
            <form id="login-form" class="form-content active" action="../../controls/log_admin.php" method="post">
                <h2 class="form-title">เข้าสู่ระบบบัญชีของคุณ</h2>
                <p class="form-subtitle">กรอกข้อมูลรับรองของคุณเพื่อเข้าถึงบัญชีของคุณ</p>
                <?php
                if (isset($_SESSION['error'])) {
                    echo "<script> alert(" . json_encode($_SESSION['error']) . "); </script>";
                    unset($_SESSION['error']);
                }

                if (isset($_SESSION['message'])) {
                    echo "<script> alert(" . json_encode($_SESSION['message']) . "); </script>";
                    unset($_SESSION['message']);
                }
                ?>
                <div class="form-group">
                    <label for="login-email">อีเมล</label>
                    <input type="email" id="login-email" name="email" placeholder="example@gmail.com" required>
                </div>

                <div class="form-group">
                    <label for="login-password">รหัสผ่าน</label>
                    <input type="password" id="login-password" name="password" placeholder="*****" required>
                </div>
                <button type="submit" class="btn btn-primary" name="admin_login">
                    <i class="fas fa-sign-in-alt"></i>
                    เข้าสู่ระบบ
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const authContainer = document.getElementById('auth-container');
            const loginTab = document.getElementById('login-tab');
            const loginForm = document.getElementById('login-form');
            const forgotPasswordLink = document.getElementById('forgot-password-link');
            const forgotPasswordModal = document.getElementById('forgot-password-modal');
            const closeForgotModalBtn = document.getElementById('close-forgot-modal');


            document.getElementById('close-auth').addEventListener('click', () => {
                window.location.href = "../../index.php";
            });


            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', (e) => {
                    const submitBtn = form.querySelector('button[type="submit"]');
                    submitBtn.classList.add('loading');
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                });
            });
            const confirmPasswordInput = document.getElementById('signup-confirm-password');
            const passwordInput = document.getElementById('signup-password');

            if (confirmPasswordInput && passwordInput) {
                confirmPasswordInput.addEventListener('input', () => {
                    if (passwordInput.value !== confirmPasswordInput.value) {
                        confirmPasswordInput.setCustomValidity('Passwords do not match');
                    } else {
                        confirmPasswordInput.setCustomValidity('');
                    }
                });
            }
        });
    </script>
</body>

</html>