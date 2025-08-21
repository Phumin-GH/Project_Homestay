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

    /* .homestay-list {
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
    }*/
    /* --- Homestay List Styles --- */
    .homestay-list {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }

    .homestay-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .homestay-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
    }

    .homestay-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .homestay-info {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .homestay-info h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        color: #212529;
    }

    .homestay-info p {
        margin: 0 0 1rem 0;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .homestay-info .location {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #495057;
        margin-bottom: 1.5rem;
    }

    .book-btn {
        margin-top: auto;
        /* Pushes button to the bottom */
        padding: 0.75rem 1.5rem;
        background: #1e5470;
        color: white;
        border: none;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }

    .book-btn:hover {
        background: #2a6f97;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(30, 84, 112, 0.2);
    }

    .alert {
        background-color: #fff3cd;
        color: #664d03;
        border: 1px solid #ffecb5;
        border-radius: 8px;
        padding: 1.5rem;
        text-align: center;
        font-size: 1rem;
        grid-column: 1 / -1;
        /* Make alert span all columns */
    }

    .alert i {
        margin-right: 0.75rem;
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

    .notify-display {
        background: #e32b06ff;
        /* สีแดง */
        width: 10px;
        height: 10px;
        border-radius: 50%;
        /* ทำให้เป็นวงกลม */
        display: inline-block;
        position: absolute;
        /* วางในมุมของ parent */
        animation: float 0.5s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: scale(1);
        }

        25% {
            transform: scale(1.2);
        }

        50% {
            transform: scale(1.3);
        }

        75% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }

    /* ปิดการแสดงผลเมื่อไม่ต้องแสดง notify */
    .non-notify-display {
        display: none;
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
                <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                            class="menu-label">Manage
                            Property</span></a></li>

                <li>
                    <a href="list_booking.php" class="active" title="รายการที่จองเข้ามา">
                        <i class="fa-solid fa-list-ul"></i>
                        <span class="menu-label">
                            Test
                            <?php if (!empty($homestay['Booking_id'])): ?>
                            <span class="notify-display"></span>
                            <?php else: ?>
                            <span class="non-notify-display"></span>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>

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

                <?php /* if (isset($_SESSION['message'])):*/ ?>
                <!-- <div class="alert">
                    <i class="fas fa-info-circle"></i> <?php /*echo htmlspecialchars($_SESSION['message']);*/ ?>
                </div> -->
                <?php /*unset($_SESSION['message']);*/ ?>
                <?php /*endif;*/ ?>

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
                            <h3 class="notify" data-id="<?= htmlspecialchars($house['Property_id']) ?>"></h3>
                            <p class="location">
                                <i class="fa-solid fa-location-pin"></i>
                                จ.<?php echo htmlspecialchars($house['Property_province'] . ', อ.' . $house['Property_district'] . ', ต.' . $house['Property_subdistrict']); ?>
                            </p>
                            <!-- <a class="book-btn">จองเลย</a> -->
                            <form action="checkInOut.php" method="POST" style='display:inline;'>
                                <input type="hidden" name="Property_id"
                                    value="<?= htmlspecialchars($house['Property_id']) ?>">
                                <button type='submit' class="book-btn">
                                    ดูรายละเอียด
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
    document.addEventListener("DOMContentLoaded", () => {
        const notifies = document.querySelectorAll(".notify");
        const home = document.querySelectorAll(".homestay-card");
        home.forEach(ele => {

            notifies.forEach(el => {
                let propertyId = el.dataset.id;

                fetch("../controls/notify.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "property_id=" + propertyId
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.total > 0) {
                            el.textContent = `📢 มี ${data.total} รายการที่ต้องเข้าพัก`;
                            // ele.textContent = `<h2>Tesr ${data.total}</h2>`;

                        } else {
                            el.textContent = "ไม่มีการจองวันนี้";
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        el.textContent = "Error loading notify";
                    });
            });
        });
    });




    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }

    function favorite(hostId) {
        // Implement favorite functionality here
        console.log('Favorite host: ' + hostId);
    }
    </script>
</body>

</html>