<?php
session_start();
require_once __DIR__ . "/../../api/get_ListHomestay.php";
require_once __DIR__ . "/../../controls/log_hosts.php";
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" href="../../public/images/logo.png">
    <title>จองบ้านพัก - Homestay Booking</title>
    <link rel="website icon" type="png" href="/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .main-content {
        max-width: 800px;
        margin: 0 auto;
        padding: 1.5rem;
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
        /* margin: 1.5rem 8rem 0 8rem; */
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
        width: 30rem;
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

    .alert i {
        font-size: 16pt;
        margin-right: 25px;
        animation: shake 0.25s infinite;

    }

    @keyframes shake {
        0% {
            transform: rotate(0deg);
        }

        10% {
            transform: rotate(-20deg);
        }

        20% {
            transform: rotate(-15deg);
        }

        30% {
            transform: rotate(-10deg);
        }

        40% {
            transform: rotate(-5deg);
        }

        50% {
            transform: rotate(0deg);
        }

        60% {
            transform: rotate(20deg);
        }

        70% {
            transform: rotate(15deg);
        }

        80% {
            transform: rotate(10deg);
        }

        90% {
            transform: rotate(5deg);
        }

        100% {
            transform: rotate(0deg);
        }
    }

    @media (max-width: 768px) {
        .main-content {
            padding: 1rem;
        }

        .sidebar {
            width: 60px;
        }

        .sidebar .menu-label,
        .sidebar .logo h1,
        .sidebar-footer div span {
            display: none;
        }

        /* .main-with-sidebar {
                margin-left: 60px;
                padding: 1rem;
                margin: 0 5rem;
            } */

        .page-header h1 {
            font-size: 1.8rem;
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

    /* --- Empty State --- */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border-radius: 8px;
        border: 1px dashed #dee2e6;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 2rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* --- Buttons --- */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.6rem 1.2rem;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.9rem;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-primary {
        flex: 1;
        background-color: #1e5470;
        color: white;
        width: 17rem;
    }

    .btn-primary:hover {
        background-color: #1e5470;
        transform: translateY(-1px);
    }

    .page-header {
        background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 16px;
        /* margin: 1.5rem 8rem 0 8rem; */
        margin-bottom: 3rem;
        text-align: center;
        border-bottom: 1px solid #e5e5e5;
        width: 55rem;
        overflow: hidden;


    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .page-header p {
        font-size: 1.1rem;
        opacity: 0.9;
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
                    <span class="menu-label"><?php echo  htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <div class="main-content">
                <div class="page-header">
                    <h1><i class="fa-solid fa-house-medical-circle-check"></i> การจัดการบ้านพัก</h1>
                    <p>รายชื่อบ้านของคุณทั้งหมด</p>
                </div>

                <!-- <?php /*if (isset($_SESSION['message'])):*/ ?>
                <div class="alert">
                    <i class="fas fa-info-circle"></i> <?php /*echo htmlspecialchars($_SESSION['message']);*/ ?>
                </div>
                <?php /*unset($_SESSION['message']);*/ ?> -->
                <?php /*endif;*/ ?>
                <?php if (count($list_house) > 0): ?>
                <h2>รายชื่อบ้านพักทั้งหมด ( <?php echo count($list_house) ?> )</h2>
                <section class="homestay-list">
                    <?php foreach ($list_house as $house): ?>
                    <div class="homestay-card" data-id="<?= htmlspecialchars($house['Property_id']) ?>">
                        <!-- onclick="window.location.href='detail-house.php?id=<?php /*echo htmlspecialchars($house['Property_id']); */ ?>'" -->
                        <img src="../../public/<?php echo htmlspecialchars($house['Property_image']); ?>"
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
                            <form action="edit-property.php" method="POST" style='display:inline;'>
                                <input type="hidden" name="Property_id"
                                    value="<?= htmlspecialchars($house['Property_id']) ?>">
                                <button type='submit' class="book-btn">
                                    แก้ไขข้อมูลบ้านพัก
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </section>
                <?php else: ?>
                <div class="empty-state">
                    <i class="ph ph-heart-break"></i>
                    <h3>ยังไม่มีบ้านพักที่พร้อมให้จอง</h3>
                    <p>กรุณาตรวจสอบการลงทะเบียนบ้านพัก</p>
                    <a href="host-dashboard.php" class="btn btn-primary">Browse Homestays</a>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
    <footer>
        <p>&copy; 2025 Homestay Booking. All rights reserved.</p>
    </footer>

    <script src="../../public/js/Barscript.js"></script>
    <script>
    // document.addEventListener("DOMContentLoaded", function() {
    //     const cards = document.querySelectorAll('.homestay-card');
    //     cards.forEach(card => {
    //         const propertyId = card.dataset.id;
    //         const btn = document.querySelectorAll('.book-btn');
    //         // alert("Property ID : " + propertyId);
    //         btn.forEach(btn => {
    //             btn.addEventListener('click', function() {
    //                 fetch(`edit-property.php?id=${propertyId}`)
    //                     .then(response => response.text())
    //                     .then(data => {
    //                         // Handle the response data if needed
    //                         console.log('Property data fetched successfully:',
    //                             data);
    //                         window.location.href = 'edit-property.php';
    //                     })
    //                     .catch(error => {
    //                         console.error('Error fetching property data:', error);
    //                     });

    //             });
    //         });


    //     });
    // });

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }
    </script>
</body>

</html>