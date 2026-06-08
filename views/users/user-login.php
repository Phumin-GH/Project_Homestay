<?php session_start(); ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Homestay Booking</title>
    <link rel="stylesheet" href="../../public/css/Loginstyle.css" />
    <link rel="website icon" type="png" href="/images/logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .password-strength {
        margin-top: 1rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e5e5e5;
    }

    #password-requirements {
        font-size: 0.875rem;
        color: #6c757d;
    }

    #password-requirements p {
        margin: 0.5rem 0;
        transition: color 0.3s;
    }

    #password-requirements p.valid {
        color: #1e5470;
        /* text-decoration: line-through; */
    }

    #password-requirements p.invalid {
        color: #dc3545;
        text-decoration: line-through;
    }
    </style>
</head>

<body>
    <div class="auth-container" id="auth-container">
        <button class="close-button" id="close-auth">
            <i class="fas fa-times"></i>
        </button>
        <div class="auth-header">
            <div>
                <img src="../../public/images/logo.png" style="width: 5rem; height: 5rem;">
            </div>
            <h1>ยินดีต้อนรับกลับ</h1>
            <p>เข้าสู่ระบบบัญชีของคุณหรือสร้างบัญชีใหม่</p>
        </div>
        <div class="auth-tabs">
            <div class="tab active" id="login-tab">
                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
            </div>
            <div class="tab" id="signup-tab">
                <i class="fas fa-user-plus"></i> สมัครสมาชิก
            </div>
        </div>
        <div class="auth-content">
            <form id="login-form" class="form-content active" action="../../controls/log_users.php" method="post">
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
                } ?>
                <div class="form-group">
                    <label for="login-email">อีเมล</label>
                    <input type="email" id="login-email" name="email" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label for="login-password">รหัสผ่าน</label>
                    <input type="password" id="login-password" name="password" placeholder="*****" required>
                </div>
                <a href="#" id="forgot-password-link" class="forgot-link">ลืมรหัสผ่านของคุณ?</a>
                <button type="submit" class="btn btn-primary" name="save_login">
                    <i class="fas fa-sign-in-alt"></i>
                    เข้าสู่ระบบ

                </button>
            </form>
            <form id="signup-form" class="form-content" action="../../controls/log_users.php" method="post">
                <h2 class="form-title">สร้างบัญชีใหม่</h2>
                <p class="form-subtitle">เข้าร่วมกับเราและเริ่มจองโฮมสเตย์ที่สมบูรณ์แบบของคุณ</p>

                <div class="form-group">
                    <label for="signup-email">อีเมล</label>
                    <input type="email" id="signup-email" name="email" placeholder="example@gmail.com" required>
                </div>
                <div class="form-group">
                    <label for="signup-firstname">ชื่อ</label>
                    <input type="text" id="signup-firstname" name="firstname" placeholder="ชื่อ" required>
                </div>
                <div class="form-group">
                    <label for="signup-lastname">นามสกุล</label>
                    <input type="text" id="signup-lastname" name="lastname" placeholder="นามสกุล" required>
                </div>
                <div class="form-group">
                    <label for="signup-phone">เบอร์</label>
                    <input type="tel" id="signup-phone" name="phone" placeholder="เบอร์" required>
                </div>
                <div class="form-group">
                    <label for="signup-password">รหัสผ่าน</label>
                    <input type="password" id="signup-password" name="password" placeholder="สร้างรหัสผ่าน" required>
                    <div class="password-strength">
                        <div id="password-requirements">
                            <p id="check_length">อักษรภาษาอังกฤษ 8 ตัวขึ้นไป</p>
                            <p id="check_lowcase">อักษรภาษาอังกฤษ a-z อย่างน้อย 1 ตัว</p>
                            <p id="check_upcase">อักษรภาษาอังกฤษ A-Z อย่างน้อย 1 ตัว</p>
                            <p id="check_special_character">อักขระพิเศษ !@#$%^&*()_\-+{}[\]|\\</p>
                            <p id="check_number">ตัวเลข 0-9 อย่างน้อย 1 ตัว</p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="signup-confirm-password">ยืนยันรหัสผ่าน</label>
                    <input type="password" id="signup-confirm-password" name="confirm-password"
                        placeholder="ยืนยันรหัสผ่าน" required>
                </div>
                <button type="submit" class="btn btn-primary" name="save_signup" id="signup">
                    <i class="fas fa-user-plus"></i>
                    สมัครสมาชิก

                </button>
            </form>
        </div>
    </div>
    <!-- Forgot Password Modal -->
    <div class="modal-overlay" id="forgot-password">
        <div class="modal-content">
            <button class="modal-close" id="close-forgot">
                <i class="fas fa-times"></i>
            </button>
            <h2 class="form-title">รีเซ็ตรหัสผ่าน</h2>
            <p class="form-subtitle">กรอกที่อยู่อีเมลของคุณและเราจะส่งลิงก์ให้คุณเพื่อตั้งรหัสผ่านใหม่</p>
            <form id="forgot-form">
                <div class="form-group">
                    <label for="forgot-email">อีเมล</label>
                    <input type="email" id="forgot-email" placeholder="eexample@gmail.com" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    ส่งลิงก์รีเซ็ต
                </button>
                <p id="msg"></p>
            </form>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const authContainer = document.getElementById("auth-container");
        const loginTab = document.getElementById("login-tab");
        const signupTab = document.getElementById("signup-tab");
        const loginForm = document.getElementById("login-form");
        const signupForm = document.getElementById("signup-form");
        const forgotPasswordLink = document.getElementById("forgot-password-link");
        const forgotPasswordModal = document.getElementById("forgot-password");
        const closeForgotModalBtn = document.getElementById("close-forgot");
        const closeAuthBtn = document.getElementById("close-auth");
        forgotPasswordLink.addEventListener('click', (e) => {
            e.preventDefault();
            forgotPasswordModal.classList.add('active');
        });
        // ปิด modal
        closeForgotModalBtn.addEventListener('click', () => {
            forgotPasswordModal.classList.remove('active');
        });
        // Tab switching
        loginTab.addEventListener("click", () => {
            loginTab.classList.add("active");
            signupTab.classList.remove("active");
            loginForm.classList.add("active");
            signupForm.classList.remove("active");
        });
        signupTab.addEventListener("click", () => {
            signupTab.classList.add("active");
            loginTab.classList.remove("active");
            signupForm.classList.add("active");
            loginForm.classList.remove("active");
        });
        // Close auth container
        closeAuthBtn.addEventListener("click", () => {
            window.location.href = "../../index.php";
        });
        // Form submission handling
        const forms = document.querySelectorAll("form");
        forms.forEach((form) => {
            form.addEventListener("submit", (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add("loading");
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Processing...';
                setTimeout(() => {
                    form.submit();
                }, 5000);
            });
        });
        // Password confirmation validation
        const confirmPasswordInput = document.getElementById(
            "signup-confirm-password"
        );
        const passwordInput = document.getElementById("signup-password");
        if (confirmPasswordInput && passwordInput) {
            confirmPasswordInput.addEventListener("input", () => {
                if (passwordInput.value !== confirmPasswordInput.value) {
                    confirmPasswordInput.setCustomValidity("Passwords do not match");
                } else {
                    confirmPasswordInput.setCustomValidity("");
                }
            });
        }
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');
        if (tab === 'signup') {
            document.getElementById('signup-tab').classList.add('active');
            document.getElementById('signup-form').classList.add('active');
            document.getElementById('login-tab').classList.remove('active');
            document.getElementById('login-form').classList.remove('active');
        } else if (tab === 'login') {
            document.getElementById('login-tab').classList.add('active');
            document.getElementById('login-form').classList.add('active');
            document.getElementById('signup-tab').classList.remove('active');
            document.getElementById('signup-form').classList.remove('active');
        }

        const signUpPassword = document.getElementById("signup-password");
        const password = document.getElementById('password-requirements');
        const check_number = document.getElementById('check_number');
        const check_length = document.getElementById('check_length');
        const check_lowcase = document.getElementById('check_lowcase');
        const check_upcase = document.getElementById('check_upcase');
        const check_special_character = document.getElementById('check_special_character');
        const hasNumber = /\d/;
        const hasLowercase = /[a-z]/;
        const hasUppercase = /[A-Z]/;
        const hasSpecial = /[!@#$%^&*()_\-+{}[\]|\\,.?]/;
        const hasEightChars = /.{8,}/;
        const signupButton = document.getElementById('signup');
        document.getElementById('signup-phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = value.slice(0, 10);
            if (value.length > 0 && value[0] !== '0') {
                e.target.value = '';
                alert('รูปแบบเบอร์โทรไม่ถูกต้อง');
                return;
            }
            let formatted = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && i % 4 === 0) formatted += ' ';
                formatted += value[i];
            }
            e.target.value = formatted.trim();
        });
        signUpPassword.addEventListener("input", () => {
            const pwd = signUpPassword.value;
            if (hasLowercase.test(pwd)) {
                check_lowcase.classList.add('invalid');
            } else {
                check_lowcase.classList.remove('invalid');
            }
            if (hasUppercase.test(pwd)) {
                check_upcase.classList.add('invalid');
            } else {
                check_upcase.classList.remove('invalid');
            }
            if (hasNumber.test(pwd)) {
                check_number.classList.add('invalid');
            } else {
                check_number.classList.remove('invalid');
            }
            if (hasSpecial.test(pwd)) {
                check_special_character.classList.add('invalid');
            } else {
                check_special_character.classList.remove('invalid');
            }
            if (hasEightChars.test(pwd)) {
                check_length.classList.add('invalid');
            } else {
                check_length.classList.remove('invalid');
            }
            const isPasswordValid = hasLowercase.test(pwd) &&
                hasUppercase.test(pwd) &&
                hasNumber.test(pwd) &&
                hasSpecial.test(pwd) &&
                hasEightChars.test(pwd);
            signupButton.disabled = !isPasswordValid;
        });
    });
    // btn.addEventListener('click', (e) => {
    //     e.preventDefault(); // ป้องกัน form submit

    //     const pwd = signUpPassword.value; // เอาค่าปัจจุบัน
    //     if (!pwd.match(strong)) {
    //         // ป้องกัน form submit
    //         e.preventDefault();
    //         message.textContent =
    //             "รหัสผ่านต้องแข็งแรง: มีตัวเล็ก ตัวใหญ่ ตัวเลข อักขระพิเศษ และความยาว 10+";
    //         message.style.color = "red";
    //         passwordInput.focus();
    //     }
    // });


    document.addEventListener("DOMContentLoaded", () => {
        document.getElementById('forgot-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const User_email = e.target.email.value.trim();
            const msgEl = document.getElementById('msg');
            alert('กำลังดำเนินการ');
            const res = await fetch('../../controls/forgot-password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    User_email
                })
            });
            const data = await res.json();
            msgEl.textContent = data.message;
            alert(data.message);
            window.location.reload();
        });

    });
    </script>
</body>

</html>