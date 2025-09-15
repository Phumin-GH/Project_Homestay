<?php
session_start();
include_once __DIR__ . '/../config/db_connect.php';
include_once __DIR__ . '/../controls/log_hosts.php';
if (!isset($_SESSION['Host_email'])) {
    header("Location: host-login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Homestay Booking</title>
    <link rel="website icon" type="png" href="/images/logo.png">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/profile.css">
    <link rel="stylesheet" href="../style/main-menu.css">
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
                <?php if ($hosts['Host_Status'] == 'pending_verify'): ?>
                <li><a href="addNew-property.php" title="ลงทะเบียนบ้านพักใหม่"><i class="fas fa-user-plus"></i>
                        <span class="menu-label">ลงทะเบียนบ้านพักใหม่</span></a></li>
                <?php endif; ?>
                <li><a href="host-dashboard.php" title="รายงาน"><i class="fas fa-tachometer-alt"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" class="active" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <?php if ($hosts['Host_Status'] == 'active'): ?>
                <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                            class="menu-label">Manage
                            Property</span></a></li>


                <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                            class="menu-label">List Bookings</span></a></li>
                <?php endif; ?>
                <li><a href="walkin-property.php" title="การจอง"><i class="fa-solid fa-person-walking"></i><span
                            class="menu-label">Walkin</span></a></li>
                <li><a href="../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul>

            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>

                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <div class="profile-container">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <h2>
                            <?php echo htmlspecialchars($avatar_initial); ?>
                        </h2>
                    </div>
                    <?php if (!empty($hosts)): ?>
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($hosts['Host_firstname'] . ' ' . $hosts['Host_lastname']); ?>
                    </h1>
                    <p class="profile-email"><?php echo htmlspecialchars($hosts['Host_email']); ?></p>
                    <p class="profile-email">
                        <?php echo date("เข้าใช้ครั้งแรก    d  M Y", strtotime($hosts['Create_at']));; ?></p>


                    <?php else: ?>
                    <h1 class="profile-name">ไม่พบข้อมูลผู้ใช้</h1>
                    <?php endif; ?>

                    <button class="edit-btn" onclick="editProfile()">
                        <i class="fas fa-edit"></i> แก้ไขโปรไฟล์
                    </button>
                </div>


                <!-- Profile Stats -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php /*echo $bookings_result->num_rows;*/ ?></div>
                        <div class="stat-label">การจองทั้งหมด</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /* echo $favorites_result->num_rows; */ ?></div>
                        <div class="stat-label">รายการโปรด</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /*echo $host['User_phone'] ? 'มี' : 'ไม่มี'; */ ?></div>
                        <div class="stat-label">เบอร์โทรศัพท์</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /* echo date('Y', strtotime($user['User_created_at'])); */ ?>
                        </div>
                        <div class="stat-label">ปีที่สมัคร</div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="profile-info">
                    <div class="info-grid">
                        <div class="info-section">
                            <h3>ข้อมูลส่วนตัว</h3>


                            <div class="info-item">
                                <div class="info-label">เลขบัตรประชาชน</div>
                                <div class="info-value"><?php echo htmlspecialchars($hosts['Host_IdCard']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">ชื่อ</div>
                                <div class="info-value"><?php echo htmlspecialchars($hosts['Host_firstname']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">นามสกุล</div>
                                <div class="info-value"><?php echo htmlspecialchars($hosts['Host_lastname']); ?></div>
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
                                <div class="info-label">อีเมล</div>
                                <div class="info-value"><?php echo htmlspecialchars($hosts['Host_email']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">เบอร์โทรศัพท์</div>
                                <div class="info-value">
                                    <?php echo $hosts['Host_phone'] ? htmlspecialchars($hosts['Host_phone']) : 'ไม่ระบุ'; ?>
                                </div>
                            </div>
                            <!--<div class="info-item">
                                <div class="info-label">วันที่สมัคร</div>
                                <div class="info-value">
                                    <?php /*echo date('d/m/Y', strtotime($host['Host_created_at'])); */ ?></div>
                            </div>-->
                            <div class="info-item">
                                <div class="info-label">สถานะ</div>
                                <div class="info-value">
                                    <span class="status-badge">
                                        <i class="fas fa-check-circle"></i>
                                        <?php
                                        echo isset($hosts['Host_Status']) && $hosts['Host_Status'] == 'active' ? 'ใช้งานได้' : 'ปิดใช้งาน';
                                        ?>
                                    </span>


                                </div>
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