<?php
session_start();
if (!isset($_SESSION['Host_email'])) {
    header("Location: host-login.php");
    exit();
}
require_once __DIR__ . "/../../controls/log_hosts.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์ - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .edit-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
    }

    .edit-header {
        background: #ffffff;
        border-radius: 20px;
        padding: 2.5rem 2rem;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
        position: relative;
        overflow: hidden;
    }

    .edit-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #1e5470;
    }

    .edit-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }

    .edit-header p {
        color: #666;
        font-size: 1rem;
    }

    .edit-form {
        background: #ffffff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
    }

    .form-section {
        margin-bottom: 2.5rem;
    }

    .form-section h3 {
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .form-section h3::before {
        content: '';
        width: 4px;
        height: 20px;
        background: #1e5470;
        border-radius: 2px;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: #1a1a1a;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 1rem;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.2s ease;
        background: #f8f9fa;
    }

    .form-input:focus {
        outline: none;
        border-color: #1e5470;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .form-input:disabled {
        background: #f1f3f4;
        color: #666;
        cursor: not-allowed;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .btn-group {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }

    .btn {
        padding: 1rem 2rem;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: #1e5470;
        color: white;

    }

    .btn-secondary {
        background: whitesmoke;
        color: #4a4f59ff;
        border: 2px solid #4a4f59ff;
    }

    .btn-primary:hover {
        background: #2c7aa1ff;
        color: white;

    }

    .btn-secondary:hover {
        background: #6b7280;
        color: white;

    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-danger {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .password-section {
        background: #f8f9ff;
        padding: 2rem;
        border-radius: 16px;
        border: 1px solid #e5e5e5;
        margin-top: 2rem;
    }

    .password-section h3 {
        color: #1e5470;
    }

    .help-text {
        color: #666;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-style: italic;
    }

    /* Phone Input Styles */
    .phone-input-container {
        display: flex;
        align-items: center;
        border: 1px solid #e5e5e5;
        border-radius: 12px;
        background: #f8f9fa;
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .phone-input-container:focus-within {
        border-color: #1e5470;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
    }

    .phone-prefix {
        background: #1e5470;
        color: white;
        padding: 1rem 0.75rem;
        font-weight: 600;
        font-size: 1rem;
        border-right: 1px solid #e5e5e5;
        min-width: 60px;
        text-align: center;
        user-select: none;
        cursor: default;
    }

    .phone-input {
        border: none !important;
        background: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        flex: 1;
        padding-left: 1rem;
    }

    .phone-input:focus {
        outline: none !important;
        border: none !important;
        background: transparent !important;
        box-shadow: none !important;
    }

    .phone-input:disabled {
        background: transparent !important;
        color: #666;
    }

    @media (max-width: 768px) {
        .edit-container {
            padding: 1rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .btn-group {
            flex-direction: column;
        }

        .edit-header {
            padding: 2rem 1rem;
        }

        .edit-header h1 {
            font-size: 1.5rem;
        }

        /* Mobile phone input styles */
        .phone-input-container {
            flex-direction: column;
            border-radius: 8px;
        }

        .phone-prefix {
            width: 100%;
            border-right: none;
            border-bottom: 1px solid #e5e5e5;
            padding: 0.75rem;
            min-width: auto;
        }

        .phone-input {
            padding: 1rem;
            text-align: center;
        }
    }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>
                    <img src="../../public/images/logo.png" alt="Logo" class="logo-image"
                        style="width: 3.5rem; height: 3.5rem;">
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
                <?php if ($hosts['Host_Status'] == 'pending_verify'): ?>
                <li><a href="add-property.php" title="ลงทะเบียนบ้านพักใหม่"><i class="fas fa-user-plus"></i>
                        <span class="menu-label">ลงทะเบียนบ้านพักใหม่</span></a></li>
                <?php endif; ?>
                <li><a href="host-dashboard.php" title="รายงาน"><i class="fa-solid fa-ranking-star"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="โปรไฟล์" class="active"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <?php if ($hosts['Host_Status'] == 'active'): ?>
                <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                            class="menu-label">Manage
                            Property</span></a></li>
                <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                            class="menu-label">List Bookings</span></a></li>
                <li><a href="refund_booking.php" title="การขอคืนเงิน"><i
                            class="fa-solid fa-money-bill-transfer"></i><span class="menu-label">List Refund</span></a>
                </li>
                <li><a href="walkin-property.php" title="การจอง"><i class="fa-solid fa-person-walking"></i><span
                            class="menu-label">Walkin</span></a></li>
                <?php endif; ?>
                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul> class="menu-label">Logout</span></a></li>
            </ul>

            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <div class="edit-container">
                <div class="edit-header">
                    <h1><i class="fas fa-user-edit"></i> แก้ไขโปรไฟล์</h1>
                    <p>อัปเดตข้อมูลส่วนตัวของคุณ</p>
                </div>
                <form class="edit-form" method="POST" action="../../controls/log_hosts.php">
                    <div class="form-section">
                        <h3>ข้อมูลส่วนตัว</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">ชื่อ <span style="color:red">*</span></label>
                                <input type="text" name="firstname" class="form-input"
                                    value="<?php echo htmlspecialchars($hosts['Host_firstname']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">นามสกุล <span style="color:red">*</span></label>
                                <input type="text" name="lastname" class="form-input"
                                    value="<?php echo htmlspecialchars($hosts['Host_lastname']); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">อีเมล</label>
                            <input type="email" class="form-input"
                                value="<?php echo htmlspecialchars($hosts['Host_email']); ?>" disabled>
                            <div class="help-text">ไม่สามารถเปลี่ยนอีเมลได้</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">เลขบัตรประชาชน</label>
                            <input type="email" class="form-input"
                                value="<?php echo htmlspecialchars($hosts['Host_IdCard']); ?>" disabled>
                            <div class="help-text">ไม่สามารถเปลี่ยนเลขบัตรประชาชนได้</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">เบอร์โทรศัพท์ <span style="color:red">*</span></label>
                            <div class="phone-input-container">

                                <input type="tel" name="phone" class="form-input phone-input"
                                    value="<?php echo htmlspecialchars($hosts['Host_phone'] ?? ''); ?>"
                                    placeholder="081-234-5678">
                            </div>
                        </div>
                    </div>
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
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" name="save_edit">
                            <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                        <button type="button" onclick=window.history.back(); class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> กลับไปโปรไฟล์
                        </button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.querySelector('.phone-input');
        const phonePrefix = document.querySelector('.phone-prefix');
        if (phoneInput && phonePrefix) {
            // Prevent editing the prefix
            phonePrefix.addEventListener('click', function(e) {
                e.preventDefault();
                phoneInput.focus();
            });
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 9) {
                    value = value.substring(0, 9);
                }
                if (value.length >= 6) {
                    value = value.substring(0, 3) + '-' + value.substring(3, 6) + '-' + value.substring(
                        6);
                } else if (value.length >= 3) {
                    value = value.substring(0, 3) + '-' + value.substring(3);
                }
                e.target.value = value;
            });
            phonePrefix.addEventListener('paste', function(e) {
                e.preventDefault();
                phoneInput.focus();
            });
            phonePrefix.addEventListener('drop', function(e) {
                e.preventDefault();
            });
            phonePrefix.addEventListener('dragover', function(e) {
                e.preventDefault();
            });
        }
    });
    </script>
</body>

</html>