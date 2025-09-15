<?php
session_start();
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Homestay Booking</title>
    <link rel="website icon" type="png" href="/images/logo.png">
    <link rel="stylesheet" href="../style/Loginstyle.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    #password-strength {
        width: 100%;
        height: 8px;
        background-color: #ddd;
        border-radius: 4px;
        margin-top: 5px;
    }

    #strength-bar {
        height: 100%;
        width: 0%;
        border-radius: 4px;
        transition: width 0.3s ease;
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
                <img src="../images/logo.png" style="width: 5rem; height: 5rem;">
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
            <form id="login-form" class="form-content active" action="../controls/log_hosts.php" method="post">
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

            <form id="signup-form" class="form-content" action="../controls/log_hosts.php" method="post">
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
                    <div id="password-strength">
                        <div id="strength-bar"></div>
                        <p id="password-message"></p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="signup-confirm-password">Confirm Password</label>
                    <input type="password" id="signup-confirm-password" name="confirm-password"
                        placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn btn-primary" name="host_signup">
                    <i class="fas fa-user-plus"></i>
                    Create Account
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
            window.location.href = "../index.php";
        });

        // Form submission handling
        const forms = document.querySelectorAll("form");
        forms.forEach((form) => {
            form.addEventListener("submit", (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.classList.add("loading");
                submitBtn.innerHTML =
                    '<i class="fas fa-spinner fa-spin"></i> Processing...';
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

        const message = document.getElementById("password-message");
        const strengthBar = document.getElementById("strength-bar");
        const btn = document.getElementById("signup-submit");
        const signUpPassword = document.getElementById("signup-password");
        let strong =
            /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+{}[\]|\\,.?])[A-Za-z\d!@#$%^&*()_\-+{}[\]|\\,.?]{10,}$/;
        let medium = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;
        let weak = /^.{1,}$/;
        signUpPassword.addEventListener("input", () => {
            const pwd = signUpPassword.value;
            if (pwd.match(strong)) {
                strengthBar.style.width = "100%";
                strengthBar.style.backgroundColor = "green";
                message.innerHTML =
                    "รหัสผ่านแข็งแรง: มีตัวเล็ก ตัวใหญ่ ตัวเลข อักขระพิเศษ และความยาว 10+";
                message.style.color = "green";
            } else if (pwd.match(medium)) {
                strengthBar.style.width = "60%";
                strengthBar.style.backgroundColor = "orange";
                message.innerHTML = "รหัสผ่านปานกลาง: มีตัวเล็ก ตัวใหญ่ ตัวเลข และความยาว8ตัวขึ้นไป";
                message.style.color = "orange";
            } else if (pwd.match(weak)) {
                strengthBar.style.width = "30%";
                strengthBar.style.backgroundColor = "red";
                message.innerHTML = "รหัสผ่านอ่อนแอ: ต้องมีตัวเล็ก ตัวใหญ่ ตัวเลข และความยาว8ตัวขึ้นไป";
                message.style.color = "red";
            } else {
                strengthBar.style.width = "0%";
                message.innerHTML = "รหัสผ่าน: ต้องมีตัวเล็ก ตัวใหญ่ ตัวเลข และความยาว8ตัวขึ้นไป";

            }
        });
        btn.addEventListener('click', (e) => {
            e.preventDefault(); // ป้องกัน form submit

            const pwd = signUpPassword.value; // เอาค่าปัจจุบัน
            if (!pwd.match(strong)) {
                // ป้องกัน form submit
                e.preventDefault();
                message.textContent =
                    "รหัสผ่านต้องแข็งแรง: มีตัวเล็ก ตัวใหญ่ ตัวเลข อักขระพิเศษ และความยาว 10+";
                message.style.color = "red";
                passwordInput.focus();
            }
        });
        document.getElementById('forgot-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const Host_email = e.target.email.value.trim();
            const msgEl = document.getElementById('msg');

            const res = await fetch('../controls/forgot-password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    Host_email
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