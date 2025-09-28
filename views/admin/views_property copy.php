<?php
session_start();
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}
require_once  __DIR__ . "/../../controls/log_hosts.php";
require_once __DIR__ . "/../../api/get_ListHomestay.php";
require_once __DIR__ . "/../../api/get_listFavorites.php";
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../public/css/style.css" />
    <link rel="stylesheet" href="../../public/css/main-menu.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <title>รายละเอียดที่พัก - <?php echo htmlspecialchars($property['Property_name']); ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f6fafd;
            margin: 0;
            color: #222;
        }

        .container {
            max-width: 70rem;
            margin: 2rem auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
            padding: 2rem;
        }

        .header {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
        }

        .main-img {
            width: 340px;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        .info {
            flex: 1;
        }

        .info h2 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .info .address {
            color: #555;
            margin-bottom: 0.7rem;
        }

        .info .host {
            color: #1e5470;
            font-weight: 500;
        }

        .info .type {
            color: #888;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }


        .section-title {
            font-size: 1.2rem;
            font-weight: 500;
            margin: 2rem 0 1rem 0;
        }





        @media (max-width: 900px) {
            .header {
                flex-direction: column;
                align-items: stretch;
            }

            .main-img {
                width: 100%;
                height: 200px;
            }
        }



        .title {
            display: flex;
            align-items: center;
            /* จัดให้อยู่กึ่งกลางแนวตั้ง */
            justify-content: space-between;
            /* ดัน h2 ไปซ้าย, ปุ่มไปขวา */
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
                <li><a href="profile.php" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <?php if ($hosts['Host_Status'] == 'active'): ?>
                    <li><a href="manage-property.php" title="จัดการบ้านพัก" class="active"><i class="fas fa-plus"></i><span
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
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"
                        title="footer"><?php echo htmlspecialchars($_SESSION['Admin_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <div class="container">
                <div class="header">
                    <img src="../../public/<?php echo htmlspecialchars($property['Property_image']); ?>"
                        class="main-img" alt="<?php echo htmlspecialchars($property['Property_name']); ?>">
                    <div class="info">
                        <div class="title">
                            <h2><?php echo htmlspecialchars($property['Property_name']); ?></h2>
                        </div>
                        <div class="address"><i class="fa fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($property['Property_province'] . ', ' . $property['Property_district'] . ', ' . $property['Property_province']); ?>
                        </div>
                        <div>
                            <span style="color:#f5b301; font-size:1.1rem; margin-left:0.5rem;">
                                <i class="fa-solid fa-star"></i>
                            </span>
                            <?php echo round($property['Rating'], 1) ?>
                        </div>
                        <div class="host">โฮสต์:
                            <?php echo htmlspecialchars($property['Host_firstname'] . ' ' . $property['Host_lastname']); ?>
                        </div>
                    </div>
                </div>

            </div>

            <div class="container" style="margin-top:2rem;">
                <div class="section-title">รีวิวจากผู้เข้าพัก</div>
                <?php if (count($reviews) > 0): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div style="border-bottom:1px solid #eee; padding:1rem 0;">
                            <div style="font-weight:600; color:#1a7f37; color: #1e5470;">
                                <?php echo htmlspecialchars($review['User_email']); ?>
                                <span style="color:#f5b301; font-size:1.1rem; margin-left:0.5rem;">
                                    <?php for ($i = 0; $i < (int)$review['Rating']; $i++) echo '<i class="fa-solid fa-star"></i>'; ?>
                                </span>
                                <span style="color:#888; font-size:0.95rem; margin-left:1rem;">
                                    <?php echo date('d/m/Y', strtotime($review['Create_at'])); ?>
                                </span>
                                <button
                                    style="border: none; background: #fff; padding: 10px; border-radius: 15px; cursor: pointer;   "><i
                                        class="fa-solid fa-ellipsis-vertical"></i></button>
                            </div>
                            <div style="margin-top:0.3rem; color:#333;">
                                <?php echo nl2br(htmlspecialchars($review['Comment'])); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="color:#888;">ยังไม่มีรีวิวสำหรับที่พักนี้</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>
</body>

</html>