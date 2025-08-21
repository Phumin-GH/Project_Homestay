<?php
session_start();
include "../config/db_connect.php";

if (!isset($_SESSION['Host_email'])) {
    header("Location: host-login.php");
    exit();
}

$email = $_SESSION["Host_email"];

// ดึงข้อมูลทั้งหมดของ host
$stmt = $conn->prepare("
    SELECT Host_firstname, Host_lastname, Create_at, Host_phone, Host_email, Host_IdCard 
    FROM host 
    WHERE Host_email = ?
");
$stmt->execute([$email]);
// ถ้าต้องการ fetchAll (เช่นเก็บ array ของหลายแถว เพื่อใช้กับ loop)
$hosts = $stmt->fetchAll(PDO::FETCH_ASSOC); // ถ้าอยากเก็บไว้ใช้ต่อ แต่จริง ๆ fetchAll หลัง fetch จะว่างแล้ว
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/profile.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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
        content: "";
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
        color: #fff;
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
        content: "";
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

    /* .edit-btn:hover {
        transform: translateY(-2px);
    } */

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
    </style>
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
                <li><a href="host-dashboard.php" title="รายงาน"><i class="fas fa-tachometer-alt"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" class="active" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                            class="menu-label">Manage
                            Property</span></a></li>

                <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                            class="menu-label">Test</span></a></li>
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
                        <!-- <i class="fas fa-user"></i> -->
                        <?php include '../controls/avatar.php'?>
                        <?php echo htmlspecialchars($avatar_initial); ?>
                    </div>

                    <?php if (!empty($hosts)): ?>
                    <?php foreach ($hosts as $host): ?>
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($host['Host_firstname'] . ' ' . $host['Host_lastname']); ?>
                    </h1>
                    <p class="profile-email"><?php echo htmlspecialchars($host['Host_email']); ?></p>
                    <p class="profile-email">
                        <?php echo date("เข้าใช้ครั้งแรก    d  M Y", strtotime($host['Create_at']));  ; ?></p>

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

                            <?php foreach ($hosts as $host): ?>
                            <div class="info-item">
                                <div class="info-label">เลขบัตรประชาชน</div>
                                <div class="info-value"><?php echo htmlspecialchars($host['Host_IdCard']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">ชื่อ</div>
                                <div class="info-value"><?php echo htmlspecialchars($host['Host_firstname']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">นามสกุล</div>
                                <div class="info-value"><?php echo htmlspecialchars($host['Host_lastname']); ?></div>
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
                                <div class="info-value"><?php echo htmlspecialchars($host['Host_email']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">เบอร์โทรศัพท์</div>
                                <div class="info-value">
                                    <?php echo $host['Host_phone'] ? htmlspecialchars($host['Host_phone']) : 'ไม่ระบุ'; ?>
                                </div>
                            </div>
                            <!--<div class="info-item">
                                <div class="info-label">วันที่สมัคร</div>
                                <div class="info-value">
                                    <?php /*echo date('d/m/Y', strtotime($host['Host_created_at'])); */?></div>
                            </div>-->
                            <div class="info-item">
                                <div class="info-label">สถานะ</div>
                                <div class="info-value">
                                    <span class="status-badge">
                                        <i class="fas fa-check-circle"></i>
                                        <?php
echo isset($host['Host_Status']) && $host['Host_Status'] == 1 ? 'ใช้งานได้' : 'ปิดใช้งาน';
?>
                                    </span>


                                </div>
                            </div>
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