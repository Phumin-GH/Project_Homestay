<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/Loginstyle.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
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
            color: #bc4a04;
            text-decoration: line-through;
        }
    </style>
</head>

<body>

    <div class="auth-container" id="auth-container">
        <button class="close-button" id="close-auth">
            <i class="fas fa-times"></i>
        </button>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<script> alert(" . json_encode($_SESSION['error']) . "); </script>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['msg'])) {
            echo "<script> alert(" . json_encode($_SESSION['msg']) . "); </script>";
            unset($_SESSION['message']);
        }
        ?>
        <div class="auth-header">
            <div>
                <img src="../../public/images/logo.png" style="width: 5rem; height: 5rem;">
            </div>
            <h1>Welcome Host to Back</h1>
            <p>Sign in to your account or create a new one</p>
        </div>

        <div class="auth-tabs">
            <div class="tab active" id="login-tab">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </div>
            <div class="tab" id="signup-tab">
                <i class="fas fa-user-plus"></i> Sign Up
            </div>
        </div>

        <div class="auth-content">
            <form id="login-form" class="form-content active" action="../../controls/log_hosts.php" method="post">
                <h2 class="form-title">Sign In to Your Account</h2>
                <p class="form-subtitle">Enter your credentials to access your account</p>

                <div class="form-group">
                    <label for="login-email">Email Address</label>
                    <input type="email" id="login-email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="login-password">Password</label>
                    <input type="password" id="login-password" name="password" placeholder="Enter your password"
                        required>
                </div>

                <a href="#" id="forgot-password-link" class="forgot-link">Forgot your password?</a>

                <button type="submit" class="btn btn-primary" name="host_login">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </button>
            </form>

            <div id="signup-form" class="form-content" action="../../controls/log_hosts.php" method="post">
                <h2 class="form-title">Create New Account</h2>
                <p class="form-subtitle">Join us and start booking your perfect homestay</p>
                <div class="form-group">
                    <label for="login-id-card">ID Card</label>
                    <input type="id_card" id="signup-id_card" name="id_card" placeholder="Enter your ID Card" required>
                </div>
                <div class="form-group">
                    <label for="signup-email">Email Address</label>
                    <input type="email" id="signup-email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="signup-firstname">First Name</label>
                    <input type="text" id="signup-firstname" name="firstname" placeholder="Enter your first name"
                        required>
                </div>

                <div class="form-group">
                    <label for="signup-lastname">Last Name</label>
                    <input type="text" id="signup-lastname" name="lastname" placeholder="Enter your last name" required>
                </div>

                <div class="form-group">
                    <label for="signup-phone">Phone Number</label>
                    <input type="tel" id="signup-phone" name="phone" placeholder="Enter your phone number" required>
                </div>

                <div class="form-group">
                    <label for="signup-password">Password</label>
                    <input type="password" id="signup-password" name="password" placeholder="Create a password"
                        required>
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
                    <label for="signup-confirm-password">Confirm Password</label>
                    <input type="password" id="signup-confirm-password" name="confirm-password"
                        placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn btn-primary" name="host_signup" id="signup">
                    <i class="fas fa-user-plus"></i>
                    Create Account
                </button>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div class="modal-overlay" id="forgot-password">
            <div class="modal-content">
                <button class="close-button" id="close-forgot">
                    <i class="fas fa-times"></i>
                </button>


                <h2 class="form-title">Reset Password</h2>
                <p class="form-subtitle">Enter your email address and we'll send you a link to reset your password.</p>

                <form id="forgot-form">
                    <div class="form-group">
                        <label for="forgot-email">Email Address</label>
                        <input type="email" id="forgot-email" placeholder="Enter your email" name="email" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Send Reset Link
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
                closeAuthBtn.addEventListener("click", () => {
                    window.location.href = "../../index.php";
                });
                const forms = document.querySelectorAll("form");
                forms.forEach((form) => {
                    form.addEventListener("submit", (e) => {
                        const submitBtn = form.querySelector('button[type="submit"]');
                        submitBtn.classList.add("loading");
                        submitBtn.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    });
                });
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
                const check_number = document.getElementById('check_number');
                const check_length = document.getElementById('check_length');
                const check_lowcase = document.getElementById('check_lowcase');
                const check_upcase = document.getElementById('check_upcase');
                const check_special_character = document.getElementById('check_special_character');
                const password = document.getElementById('password-requirements');
                const signUpPassword = document.getElementById("signup-password");
                const signupButton = document.getElementById('signup');
                const hasNumber = /\d/;
                const hasLowercase = /[a-z]/;
                const hasUppercase = /[A-Z]/;
                const hasSpecial = /[!@#$%^&*()_\-+{}[\]|\\,.?]/;
                const hasEightChars = /.{8,}/;
                document.getElementById('signup-id_card').addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    value = value.slice(0, 13);
                    let formatted = '';
                    for (let i = 0; i < value.length; i++) {
                        if (i > 0 && i % 4 === 0) formatted += ' ';
                        formatted += value[i];
                    }
                    e.target.value = formatted.trim();
                });
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
            document.addEventListener('DOMContentLoaded', () => {
                document.getElementById('forgot-form').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const Host_email = e.target.email.value.trim();
                    alert('กำลังดำเนินการ');
                    const res = await fetch('../../controls/forgot-password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            Host_email
                        })
                    });
                    const data = await res.json();
                    // msgEl.textContent = data.message;
                    // alert(data.message);
                    window.location.reload();
                });
            });
        </script>
</body>

</html>