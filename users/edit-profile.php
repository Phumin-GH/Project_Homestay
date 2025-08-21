<?php
session_start();
if (!isset($_SESSION['User_email'])) {
    header("Location: user-login.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์ - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/profile.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="../style/barStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <header>

        <nav class="navbar">
            <div class="logo">
                <h1>
                    <img src="../images/logo.png" alt="Logo" class="logo-image" style="width: 3.5rem; height: 3.5rem;">
                    Homestay bookings
                </h1>
            </div>
        </nav>
    </header>

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <button class="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="sidebar-menu">
                <li><a href="main-menu.php"><i class="fas fa-home"></i><span class="menu-label">Home</span></a></li>
                <li><a href="profile.php" class="active"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a></li>
                <li><a href="favorites.php"><i class="fas fa-heart"></i><span class="menu-label">Favorite</span></a>
                </li>
                <li><a href="bookings.php"><i class="fas fa-calendar"></i><span class="menu-label"
                            title="รายการจอง">Bookings</span></a>
                </li>
                <li><a href="reviews.php" title="รีวิวสถานที่พัก"><i class="fas fa-star"></i><span
                            class="menu-label">Review</span></a></li>
                <li><a href="../controls/logout.php"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>

                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['User_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <div class="edit-container">
                <!-- Edit Header -->
                <div class="edit-header">
                    <h1><i class="fas fa-user-edit"></i> แก้ไขโปรไฟล์</h1>
                    <p>อัปเดตข้อมูลส่วนตัวของคุณ</p>
                </div>

                <?php /*echo $message;*/ ?>

                <!-- Edit Form -->
                <form class="edit-form" method="POST" action="../controls/log_users.php">
                    <?php
if (!isset($message)) {
    $message = '';
}
?>
                    <!-- Personal Information -->
                    <?php include "../controls/log_users.php"?>
                    <?php foreach ($users as $user): ?>
                    <div class="form-section">
                        <h3>ข้อมูลส่วนตัว</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">ชื่อ <span style="color:red">*</span></label>
                                <input type="text" name="firstname" class="form-input"
                                    value="<?php echo htmlspecialchars($user['Firstname']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">นามสกุล <span style="color:red">*</span></label>
                                <input type="text" name="lastname" class="form-input"
                                    value="<?php echo htmlspecialchars($user['Lastname']); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">อีเมล</label>
                            <input type="email" class="form-input"
                                value="<?php echo htmlspecialchars($user['User_email']); ?>" disabled>
                            <div class="help-text">ไม่สามารถเปลี่ยนอีเมลได้</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">เบอร์โทรศัพท์ <span style="color:red">*</span></label>
                            <div class="phone-input-group">

                                <input type="tel" name="phone" class="form-input phone-input"
                                    value="<?php echo htmlspecialchars($user['Phone'] ?? ''); ?>"
                                    placeholder="081-234-5678">
                            </div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="password-section">
                        <h3>เปลี่ยนรหัสผ่าน</h3>
                        <div class="form-group">
                            <label class="form-label">รหัสผ่านปัจจุบัน</label>
                            <input type="password" name="current_password" class="form-input"
                                placeholder="กรอกรหัสผ่านปัจจุบัน">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">รหัสผ่านใหม่</label>
                                <input type="password" name="new_password" class="form-input"
                                    placeholder="รหัสผ่านใหม่">
                            </div>
                            <div class="form-group">
                                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" name="confirm_password" class="form-input"
                                    placeholder="ยืนยันรหัสผ่านใหม่">
                            </div>
                        </div>
                        <div class="help-text">* ปล่อยว่างหากไม่ต้องการเปลี่ยนรหัสผ่าน</div>
                    </div>
                    <?php endforeach; ?>
                    <!-- Buttons -->
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" name="save_edit">
                            <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                        <a href="profile.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> กลับไปโปรไฟล์
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle("collapsed");
    }

    // Phone number input handling
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.querySelector('.phone-input');

        if (phoneInput) {
            // Format phone number as user types
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, ''); // Remove non-digits

                // Limit to 10 digits (Thai phone number)
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }

                // Format as XXX-XXX-XXXX
                if (value.length >= 6) {
                    value = value.substring(0, 3) + '-' + value.substring(3, 6) + '-' + value.substring(
                        6);
                } else if (value.length >= 3) {
                    value = value.substring(0, 3) + '-' + value.substring(3);
                }

                e.target.value = value;
            });

            // Prevent non-numeric input
            phoneInput.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                if (!/\d/.test(char)) {
                    e.preventDefault();
                }
            });

            // Handle paste event
            phoneInput.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const numericValue = pastedText.replace(/\D/g, '');

                if (numericValue.length <= 10) {
                    this.value = numericValue;
                    this.dispatchEvent(new Event('input'));
                }
            });
        }
    });
    </script>
</body>

</html>