<?php 
session_start();
require_once __DIR__ . "/../config/db_connect.php";

if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}
$host_email = $_SESSION['Host_email'];
$stmt = $conn->prepare("SELECT Host_id FROM host WHERE Host_email = ?");
$stmt->execute([$host_email]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$host_id = $row ? $row['Host_id'] : null;
$stmt = null;
// Fetch approved properties for display
if ($host_id) {
    // ดึง property ที่อนุมัติแล้ว
    $sql = "
        SELECT p.*, h.Host_firstname, h.Host_lastname 
        FROM Property p 
        INNER JOIN Host h ON p.Host_id = h.Host_id 
        WHERE h.Host_id = ? AND p.Property_status = 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$host_id]);
    $homestay = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = null;
} else {
    $homestay = [];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="../images/logo1.png">
    <title>จองบ้านพัก - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .main-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 1.5rem;
    }

    .page-header {
        background: none;
        color: #1a1a1a;
        padding: 1rem 0;
        margin-bottom: 1.5rem;
        text-align: left;
        border-bottom: 1px solid #e5e5e5;
        width: 55rem;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 500;
        margin: 0;
    }

    .homestay-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .homestay-card {
        background: #ffffff;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        overflow: hidden;
        width: 55rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 1rem;
    }

    .homestay-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .homestay-card img {
        width: 25rem;
        height: 15rem;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 1rem;
    }

    .homestay-info {
        flex: 1;
    }

    .homestay-info h3 {
        font-size: 1.2rem;
        font-weight: 500;
        margin: 0 0 0.5rem;
        color: #1a1a1a;
    }

    .homestay-info p {
        font-size: 0.9rem;
        color: #666;
        margin: 0.3rem 0;
    }

    .location {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #1e5470;
    }

    .book-btn {
        display: inline-block;
        padding: 0.5rem 1rem;
        background: #3480a7ff;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.9rem;
        margin-top: 0.5rem;
        transition: background-color 0.2s ease;
    }

    .book-btn:hover {
        background: #9cdeffff;
    }

    .favorite-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
        color: #dc3545;
        position: absolute;
        top: 1rem;
        right: 1rem;
    }

    .alert {
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        background: #e7f4ff;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 1rem;
        }

        .homestay-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .homestay-card img {
            width: 100%;
            height: auto;
            margin-right: 0;
            margin-bottom: 1rem;
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
                    Homestay Bookings
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
                <li><a href="profile.php" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <li><a href="manage-property.php" class="active" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
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
                    <span class="menu-label"><?php echo  htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <div class="main-content">
                <div class="page-header">
                    <h1>บ้านพักทั้งหมด (<?php echo count($homestay)?>)</h1>
                </div>

                <?php if (isset($_SESSION['message'])): ?>
                <div class="alert">
                    <i class="fas fa-info-circle"></i> <?php echo htmlspecialchars($_SESSION['message']); ?>
                </div>
                <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <section class="homestay-list">

                    <?php if (empty($homestay)): ?>
                    <div class="alert">
                        <i class="fas fa-info-circle"></i> ยังไม่มีบ้านพักที่พร้อมให้จอง
                    </div>
                    <?php else: ?>
                    <?php foreach ($homestay as $house): ?>


                    <div class="homestay-card" data-id="<?= htmlspecialchars($house['Property_id']) ?>">
                        <!-- onclick="window.location.href='detail-house.php?id=<?php /*echo htmlspecialchars($house['Property_id']); */?>'" -->
                        <img src="../<?php echo htmlspecialchars($house['Property_image']); ?>"
                            alt="<?php echo htmlspecialchars($house['Property_name']); ?>">
                        <div class="homestay-info">
                            <h3><?php echo htmlspecialchars($house['Property_name']); ?></h3>

                            <p>เจ้าของ:
                                <?php echo htmlspecialchars($house['Host_firstname'] . ' ' . $house['Host_lastname']); ?>
                            </p>

                            <p class="location">
                                <i class="fa-solid fa-location-pin"></i>
                                จ.<?php echo htmlspecialchars($house['Property_province'] . ', อ.' . $house['Property_district'] . ', ต.' . $house['Property_subdistrict']); ?>
                            </p>
                            <!-- <a class="book-btn">จองเลย</a> -->
                            <form action="edit-property.php" method="POST" style='display:inline;'>
                                <input type="hidden" name="Property_id"
                                    value="<?= htmlspecialchars($house['Property_id']) ?>">
                                <button type='submit' class="book-btn">
                                    แก้ไขข้อมูลบ้านพัก
                                </button>
                            </form>

                            <!-- <button class="favorite-btn" onclick="favorite(<?php /*echo htmlspecialchars($house['Host_id']);*/ ?>);
                                event.stopPropagation();">
                                <i class="fas fa-heart"></i>

                            </button> -->
                        </div>
                    </div>


                    <?php endforeach; ?>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Homestay Booking. All rights reserved.</p>
    </footer>

    <script src="../script/Barscript.js"></script>
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }
    </script>
</body>

</html>