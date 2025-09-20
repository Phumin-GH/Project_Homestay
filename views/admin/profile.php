<?php
session_start();

include_once __DIR__ . '/../../controls/log_admin.php';
if (!isset($_SESSION['Admin_email'])) {
    header("Location: admin-login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="../../public/css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
                <ul class="sidebar-menu">
                    <li><a href="admin-dashboard.php" title="หน้าแดชบอร์ด"><i class="fas fa-tachometer-alt"></i><span
                                class="menu-label">Dashboard</span></a></li>
                    <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน" class="active"><i class="fas fa-user"></i><span
                                class="menu-label">Profile</span></a></li>
                    <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก"><i
                                class="fas fa-check-circle"></i><span class="menu-label">Approve Properties</span></a>
                    </li>
                    <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                                class="menu-label">Hosts</span></a></li>
                    <li><a href="manage-users.php" title="จัดการผู้ใช้งาน"><i class="fas fa-user-friends"></i><span
                                class="menu-label">Users</span></a></li>
                    <li><a href="manage-reviews.php" title="รีวิวจากผู้ใช้งาน"><i class="fas fa-star"></i><span
                                class="menu-label">Reviews</span></a></li>
                    <li><a href="violations.php" title="รายการการละเมิด"><i
                                class="fas fa-exclamation-triangle"></i><span class="menu-label">Violations</span></a>
                    </li>
                    <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                                class="menu-label">Logout</span></a></li>
                </ul>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Admin_email']); ?></span>
                </div>
            </div>
        </aside>
        <?php
        if (isset($_SESSION['error'])) {
            echo "<script> alert(" . json_encode($_SESSION['error']) . "); </script>";
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['msg'])) {
            echo "<script> alert(" . json_encode($_SESSION['msg']) . "); </script>";
            unset($_SESSION['msg']);
        }
        ?>
        <div class="main-with-sidebar">
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <h2>
                            <?php echo htmlspecialchars($avatar_initial); ?>
                        </h2>
                    </div>
                    <?php if (isset($admin)): ?>
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($admin['Admin_username']); ?>
                    </h1>
                    <p class="profile-email"><?php echo htmlspecialchars($admin['Admin_email']); ?></p>
                    <?php else: ?>
                    <h1 class="profile-name">ไม่พบข้อมูลผู้ใช้</h1>
                    <?php endif; ?>
                    <button class="edit-btn" onclick="editProfile()">
                        <i class="fas fa-edit"></i> แก้ไขโปรไฟล์
                    </button>
                </div>
                <div class="profile-info">
                    <div class="info-grid">
                        <div class="info-section">
                            <h3>ข้อมูลส่วนตัว</h3>
                            <div class="info-item">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($admin['Admin_email']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Username</div>
                                <div class="info-value"><?php echo htmlspecialchars($admin['Admin_username']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">รหัสผ่าน</div>
                                <div class="info-value">
                                    <input type="password" value="••••••••••" maxlength="10" readonly
                                        style="border: none; background: transparent;">
                                </div>
                            </div>
                        </div>
                        <div class="info-section">
                            <h3>ข้อมูลติดต่อ</h3>
                            <div class="info-item">
                                <div class="info-label">เบอร์โทรศัพท์</div>
                                <div class="info-value">
                                    <?php if ($admin['Admin_phone']): ?>
                                    <span class="phone-display">
                                        <span
                                            class="phone-number"><?php echo htmlspecialchars($admin['Admin_phone']); ?></span>
                                    </span>
                                    <?php else: ?>
                                    <span class="no-phone">ไม่ระบุ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">วันที่สมัคร</div>
                                <div class="info-value">
                                    <?php echo date('d/M/Y', strtotime($admin['Create_at']));  ?></div>
                            </div>

                        </div>
                    </div>
                </div>
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

    function editProfile() {
        window.location.href = 'edit-profile.php';
    }
    </script>
</body>

</html>