<?php
session_start();
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
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
        justify-content: space-between;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle {
        border: 1px solid transparent;
        background: transparent;
        color: #555;
        padding: 10px;
        border-radius: 50%;
        cursor: pointer;
        line-height: 1;
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }

    .dropdown-toggle:hover,
    .dropdown-toggle:focus {
        background-color: #f1f1f1;
        outline: none;

    }

    .dropdown-menu {
        display: block;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 10px;
        background-color: white;
        min-width: 240px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-radius: 12px;
        padding: 8px;
        z-index: 1000;
    }

    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);

    }

    .dropdown-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 12px 16px;
        color: #333;
        text-decoration: none;
        font-size: 0.95rem;
        white-space: nowrap;
        border-radius: 8px;
        transition: background-color 0.2s ease, color 0.2s ease;
    }

    .dropdown-item i {
        margin-right: 12px;
        width: 18px;
        text-align: center;
        color: #888;
        transition: color 0.2s ease;
    }

    .dropdown-item:hover {
        background-color: #f5f5f5;
    }

    .dropdown-item.report-review-btn:hover {
        background-color: #fff1f1;
        color: #d93025;
    }

    .dropdown-item.report-review-btn:hover i {
        color: #d93025;
    }

    .modal-report {
        display: none;
        /* เริ่มต้นซ่อน */
        position: fixed;
        z-index: 500;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-report-content {
        background-color: #fff;
        margin: 10% auto;
        padding: 20px;
        border-radius: 8px;
        width: 400px;
        position: relative;
    }

    .close-report {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 24px;
        cursor: pointer;
    }

    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        resize: vertical;
    }

    textarea:focus {
        border: #1e5470 2px solid;
        box-shadow: 0 0 6px rgba(89, 166, 254, 1);
        outline: none;
    }

    .contact-btn {
        background-color: #1e5470;
        color: white;
    }

    .contact-btn:hover {
        background-color: #2a6f97;
    }

    .cancel-btn {
        background-color: #dc3545;
        color: white;
    }

    .cancel-btn:hover {
        background-color: #c82333;
    }

    .action-btn {
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-btn:hover {
        transform: translateY(-1px);
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
                        title="footer"><?php echo htmlspecialchars($_SESSION['Host_email']); ?></span>
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
                        <!-- <button
                            style="border: none; background: #fff; padding: 10px; border-radius: 15px; cursor: pointer;   "><i
                                class="fa-solid fa-ellipsis-vertical"></i></button> -->
                        <div class="dropdown">
                            <button class="dropdown-toggle" aria-label="ตัวเลือกเพิ่มเติม">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a class="dropdown-item report-review-btn"
                                    data-review-id="<?php echo $review['Review_id'] ?>">
                                    <i class="fa-solid fa-flag"></i> รายงานรีวิวที่ไม่เหมาะสม
                                </a>
                            </div>
                        </div>

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
    <div id="reportModal" class="modal-report">
        <div class="modal-report-content">
            <span class="close-report">&times;</span>
            <h2>เหตุผลการเรื่องร้องเรียน</h2>
            <form id="reportForm">
                <input type="hidden" id="reviewIdInput">
                <label for="reason">กรุณากรอกเหตุผล:</label>
                <textarea id="reason" name="reason" rows="4" placeholder="ระบุเหตุผล..." required></textarea>
                <br>
                <button type="submit" class="action-btn contact-btn">ส่งข้อมูล</button>
                <button type="button" id="closeBtn" class="action-btn cancel-btn">ยกเลิก</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }
    document.addEventListener('DOMContentLoaded', function() {
        const allDropdownToggles = document.querySelectorAll('.dropdown-toggle');
        allDropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', function(event) {
                event.stopPropagation();
                const dropdownMenu = this.nextElementSibling;
                dropdownMenu.classList.toggle('show');
            });
        });

        window.addEventListener('click', function(event) {
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(menu => {
                if (!menu.parentElement.contains(event.target)) {
                    menu.classList.remove('show');
                }
            });
        });
        const modal = document.getElementById("reportModal");
        const report_btn = document.querySelectorAll('.report-review-btn');
        const span = document.querySelector(".close-report");
        const closeBtn = document.getElementById("closeBtn");
        const formReport = document.getElementById("reportForm");
        const reason_raw = document.getElementById('reason');
        const review_id_raw = document.getElementById('reviewIdInput');
        report_btn.forEach(btn => {
            btn.onclick = function() {
                const reviewId = this.dataset.reviewId;
                document.getElementById('reviewIdInput').value = reviewId;
                modal.style.display = "block";
                this.closest('.dropdown-menu').classList.remove('show');
            }
        })
        span.onclick = () => {
            modal.style.display = "none";
            reason.value = '';

        }
        closeBtn.onclick = () => {
            modal.style.display = "none";
            reason.value = '';
        }
        window.onclick = (e) => {
            if (e.target == modal) {
                modal.style.display = "none";
                reason.value = '';

            }
        }
        formReport.onsubmit = (e) => {
            e.preventDefault();
            const reason = reason_raw.value.trim();
            const review_id = review_id_raw.value;

            fetch('../../controls/notify.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        review_id: review_id,
                        reason: reason,
                        submit_rv_host: true
                    })
                })
                .then(res => {
                    if (!res.ok) throw new Error('network response was not ok');
                    return res.json();
                })
                .then(data => {
                    if (data.success === true) {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error.message);
                });
        };
    });
    </script>
</body>

</html>