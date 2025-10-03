<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/Loginstyle.css" />
</head>

<body>
    <a href="#" id="forgot-password-link">Forgot your password?</a>
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
        <script>
        document.getElementById('forgot-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const User_email = e.target.email.value.trim();
            const msgEl = document.getElementById('msg');

            const res = await fetch('controls/forgot-password.php', {
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
        });

        const modal = document.getElementById('forgot-password');
        const closeBtn = document.getElementById('close-forgot');
        const forgotLink = document.getElementById('forgot-password-link');

        // เปิด modal
        forgotLink.addEventListener('click', (e) => {
            e.preventDefault();
            modal.classList.add('active');
        });

        // ปิด modal
        closeBtn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
        </script>
</body>

</html>