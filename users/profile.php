<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "../config/db_connect.php";

include "../controls/log_users.php";
// include "../controls/get_users.php";
if (!isset($_SESSION['User_email'])) {
    header("Location: user-login.php");
    exit();
}
// ตรวจสอบการ logout
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['User_email']);
    header("Location: ../index.php");
    exit();
}
$email = $_SESSION["User_email"];

// ดึงข้อมูลทั้งหมดของ host
$stmt = $conn->prepare("
    SELECT Firstname, Lastname, Create_at, Phone, User_email ,User_Status
    FROM user
    WHERE User_email = ?
");
$stmt->execute([$email]);
// ถ้าต้องการ fetchAll (เช่นเก็บ array ของหลายแถว เพื่อใช้กับ loop)
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="website icon" type="png" href="/images/logo.png">
    <title>Profile - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="../style/profile.css">
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
            <div class="profile-container">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <!-- <i class="fas fa-user"></i> -->
                        <h2>
                            <?php echo htmlspecialchars($avatar_initial); ?>
                        </h2>
                    </div>

                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <h1 class="profile-name">
                                <?php echo htmlspecialchars($user['Firstname'] . ' ' . $user['Lastname']); ?>
                            </h1>
                            <p class="profile-email"><?php echo htmlspecialchars($user['User_email']); ?></p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <h1 class="profile-name">ไม่พบข้อมูลผู้ใช้</h1>
                    <?php endif; ?>

                    <button class="edit-btn" onclick="editProfile()">
                        <i class="fas fa-edit"></i> แก้ไขโปรไฟล์
                    </button>
                </div>


                <!-- Profile Stats -->
                <!-- <div class="stats-grid">
                    
                    <div class="stat-card">
                        <div class="stat-number"><?php /*echo $bookings_result->num_rows;*/ ?></div>
                        <div class="stat-label">การจองทั้งหมด</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php  /*echo $favorites_result->num_rows;*/ ?></div>
                        <div class="stat-label">รายการโปรด</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /*echo $user['User_phone'] ? 'มี' : 'ไม่มี';*/ ?></div>
                        <div class="stat-label">เบอร์โทรศัพท์</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php /*echo date('Y', strtotime($user['User_created_at']));*/ ?></div>
                        <div class="stat-label">ปีที่สมัคร</div>
                    </div>
                </div> -->

                <!-- Profile Information -->
                <div class="profile-info">
                    <div class="info-grid">
                        <div class="info-section">
                            <h3>ข้อมูลส่วนตัว</h3>
                            <?php foreach ($users as $user): ?>
                                <div class="info-item">
                                    <div class="info-label">ชื่อ</div>
                                    <div class="info-value"><?php echo htmlspecialchars($user['Firstname']); ?></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">นามสกุล</div>
                                    <div class="info-value"><?php echo htmlspecialchars($user['Lastname']); ?></div>
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
                                <div class="info-value"><?php echo htmlspecialchars($user['User_email']); ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">เบอร์โทรศัพท์</div>
                                <div class="info-value">
                                    <?php if ($user['Phone']): ?>
                                        <span class="phone-display">

                                            <span
                                                class="phone-number"><?php echo htmlspecialchars($user['Phone']); ?></span>
                                        </span>
                                    <?php else: ?>
                                        <span class="no-phone">ไม่ระบุ</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <!--<div class="info-item">
                                <div class="info-label">วันที่สมัคร</div>
                                <div class="info-value">
                                    <?php /*echo date('d/m/Y', strtotime($user['User_created_at'])); */ ?></div>
                            </div>-->
                            <div class="info-item">
                                <div class="info-label">สถานะ</div>
                                <div class="info-value">
                                    <span class="status-badge">
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo $user['User_Status'] ? htmlspecialchars($user['User_Status']) : 'ใช้งานได้'; ?>
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