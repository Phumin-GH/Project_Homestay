<?php
session_start();
include_once __DIR__ . '/../config/db_connect.php';
if (!isset($_SESSION['Admin_email'])) {
    header("Location: admin-login.php");
    exit();
}

// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['Admin_email']);
    header("Location: ../index.php");
    exit();
}
if (isset($_SESSION["Admin_email"])) {
    $email = $_SESSION["Admin_email"];
    $stmt = $conn->prepare("SELECT * FROM admin_sys WHERE Admin_email = ?");
    $stmt->execute([$email]);
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} 

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="../style/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- <style>
    .profile-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem;
    }

    .profile-header {
        background: #ffffff;
        border-radius: 20px;
        padding: 3rem 2rem;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
        position: relative;
        overflow: hidden;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: #1e5470;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: #1e5470;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2.5rem;
        color: white;

    }

    .profile-name {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1a1a1a;
    }

    .profile-email {
        font-size: 1rem;
        color: #666;
        margin-bottom: 2rem;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #ffffff;
        padding: 1.5rem;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #6e6e70ff;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #666;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .profile-info {
        background: #ffffff;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .info-section h3 {
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-section h3::before {
        content: '';
        width: 4px;
        height: 20px;
        background: #1e5470;
        border-radius: 2px;
    }

    .info-item {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e5e5e5;
    }

    .info-label {
        font-weight: 600;
        color: #1e5470;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #1a1a1a;
        font-size: 1rem;
        font-weight: 500;
    }

    .edit-btn {
        background: #1e5470;
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1rem;
        font-weight: 600;

    }

    .edit-btn:hover {
        animation: forwards;

    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #dcfce7;
        color: #166534;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-badge i {
        font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .profile-container {
            padding: 1rem;
        }

        .profile-header {
            padding: 2rem 1rem;
        }

        .profile-name {
            font-size: 1.5rem;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }
    }
    </style> -->
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
                    <li><a href="../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
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

        <div class="main-with-sidebar">
            <div class="profile-container">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <?php /*include "../controls/get_all_three.php"*/?>
                    <?php if (isset($admins)): ?>
                    <?php foreach ($admins as $admin): ?>
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($admin['Admin_username'] ); ?>
                    </h1>
                    <p class="profile-email"><?php echo htmlspecialchars($admin['Admin_email']); ?></p>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <h1 class="profile-name">ไม่พบข้อมูลผู้ใช้</h1>
                    <?php endif; ?>

                    <button class="edit-btn" onclick="editProfile()">
                        <i class="fas fa-edit"></i> แก้ไขโปรไฟล์
                    </button>
                </div>


                <!-- Profile Stats -->
                <!--<div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php /*echo $bookings_result->num_rows;*/ ?></div>
                        <div class="stat-label">การจองทั้งหมด</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /* echo $favorites_result->num_rows; */?></div>
                        <div class="stat-label">รายการโปรด</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /*echo $host['User_phone'] ? 'มี' : 'ไม่มี'; */?></div>
                        <div class="stat-label">เบอร์โทรศัพท์</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /* echo date('Y', strtotime($user['User_created_at'])); */?></div>
                        <div class="stat-label">ปีที่สมัคร</div>
                    </div>
                </div>-->

                <!-- Profile Information -->
                <div class="profile-info">
                    <div class="info-grid">
                        <div class="info-section">
                            <h3>ข้อมูลส่วนตัว</h3>
                            <?php /*include "../controls/get_all_three.php"*/?>
                            <?php foreach ($admins as $admin): ?>
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
                            <!--<div class="info-item">
                                <div class="info-label">วันที่สมัคร</div>
                                <div class="info-value">
                                    <?php /*echo date('d/m/Y', strtotime($host['Host_created_at'])); */?></div>
                            </div>-->
                            <!--<div class="info-item">
                                <div class="info-label">สถานะ</div>
                                <div class="info-value">
                                    <span class="status-badge">
                                        <i class="fas fa-check-circle"></i>
                                        <?php /* echo $host['Host_Status'] ? htmlspecialchars($host['Host_Status']) : 'ใช้งานได้'; */ ?>
                                    </span>
                                </div>
                            </div>-->
                        </div>
                        <?php endforeach; ?>
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