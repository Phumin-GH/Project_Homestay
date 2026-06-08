<?php
session_start();
if (isset($_SESSION["User_email"])) {
    $user_email = $_SESSION["User_email"];
} else {
    $_SESSION['msg'] = 'login first';

    header("Location: ../../index.php");
    exit();
}
require_once __DIR__ . "/../../api/get_ListHomestay.php";
// require_once __DIR__ . "/../../api/get_Policy.php";


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="images/jpg" href="images/logo1.png">
    <title>Homestay Booking</title>

    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css" />
    <link rel="stylesheet" href="../../public/css/main-menu.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .banner-slider {
            width: 100%;
            margin-bottom: 0.25rem;
            height: 350px;
            position: relative;
            overflow: hidden;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .banner-slider img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
            filter: brightness(0.8);
        }

        .banner-slider img.active {
            display: block;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            background-color: rgba(0, 0, 0, 0.3);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 24px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.3s ease;
            opacity: 0;
        }

        .banner-slider:hover .slider-btn {
            opacity: 1;
        }

        .slider-btn:hover {
            background-color: rgba(0, 0, 0, 0.6);
            transform: translateY(-50%) scale(1.1);
        }

        .prev {
            left: 25px;
        }

        .next {
            right: 25px;
        }

        .banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 5;
            width: 65%;
        }

        .banner-text h2 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }

        .banner-text p {
            font-size: 1.3rem;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
        }

        /* Shine Animation */
        .btn-shine {
            background: linear-gradient(99deg,
                    transparent,
                    rgba(255, 255, 255, 0.4),
                    transparent);
            background-size: 200% 100%;
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% {
                background-position: -200% 0;
            }

            50% {
                background-position: 100% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        .alert {
            padding: 0.75rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .alert-warning {
            background: #fff8e1;
            border-color: #ffe082;
            color: #8a6d3b;
        }

        /* Modal Styles */
        .modal-info {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: #ffffff;
            margin: 10% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
            overflow: hidden;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .close {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .close:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 2rem;

        }

        .modal-body .greeting {
            /* text-align: center; */
            font-size: 2rem;
            color: #1e5470;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .modal-body .message {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
            align-content: start;
            margin-bottom: 1.5rem;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        .modal-footer {
            padding: 1rem 2rem 2rem;
            text-align: center;
        }

        .btn-modal {
            background: #1e5470;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-modal:hover {
            background: #74adc9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 84, 112, 0.3);
        }

        @media (max-width: 768px) {
            .modal-content {
                margin: 20% auto;
                width: 95%;
            }

            .modal-header {
                padding: 1rem 1.5rem;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .modal-body .greeting {
                font-size: 1.5rem;
            }
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
    <?php
    if (isset($_SESSION['error'])) {
        // echo "<div class='alert alert-danger'><i class='fa-solid fa-ban'></i>" . $_SESSION['error'] . "</div>";
        echo "<script> alert(" . json_encode($_SESSION['error']) . "); </script>";
        unset($_SESSION['error']);
    }
    if (isset($_SESSION['message'])) {
        // echo "<div class='alert alert-success'><i class='fa-solid fa-check'></i>" . $_SESSION['message'] . "</div>";
        echo "<script> alert(" . json_encode($_SESSION['message']) . "); </script>";
        unset($_SESSION['message']);
    } ?>
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <button class="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="sidebar-menu">
                <li><a href="main-menu.php" class="active" title="หน้าหลัก"><i class="fa-solid fa-house"></i><span
                            class="menu-label">หน้าหลัก</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">ข้อมูลผู้ใช้งาน</span></a></li>

                <li><a href="favorites_rl.php" title="รายการโปรด"><i class="fas fa-heart"></i><span
                            class="menu-label">รายการโปรด</span></a>
                </li>
                <li><a href="bookings.php"><i class="fas fa-calendar"></i><span class="menu-label"
                            title="รายการจอง">รายการจอง</span></a>
                </li>

                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">ออกจากระบบ</span></a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"
                        title="อีเมล"><?php echo htmlspecialchars($_SESSION['User_email']); ?></span>
                </div>
            </div>
        </aside>


        <!-- Main -->
        <div class="main-with-sidebar">
            <main>
                <div class="banner-slider">
                    <img src="../../public/images/logo/banner1.jpg" class="active" alt="Banner">
                    <img src="../../public/images/logo/banner2.jpg" alt="Banner">
                    <img src="../../public/images/logo/banner3.jpg" alt="Banner">
                    <img src="../../public/images/logo/banner4.jpg" alt="Banner">
                    <img src="../../public/images/logo/banner5.jpg" alt="Banner">
                    <img src="../../public/images/logo/banner6.jpeg" alt="Banner">

                    <div class="banner-text">
                        <h2 class="btn-shine">ยินดีต้อนรับ</h2>
                        <p class="btn-shine">ค้นหาที่หลบพักผ่อนที่ลงตัวสำหรับคุณ</p>
                    </div>

                    <button class="slider-btn prev">&#10094;</button>
                    <button class="slider-btn next">&#10095;</button>
                </div>

                <section class="search-section">
                    <div class="search-container">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="ค้นหาบ้านพัก จังหวัด อำเภอ..." />
                            <!-- <input type="date" id="checkInDate" />
                            <input type="date" id="checkOutDate" /> -->
                            <select name="filter" id="filter">
                                <option value="">คัดกรอง</option>
                                <option value="best_reviews">คะแนนรีวิวดีสุด</option>
                                <option value="rate_poor">ราคาถูกที่สุด</option>
                                <option value="popular">ยอดนิยม</option>
                            </select>
                            <button type="button" id="searchBtn"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </section>

                <section class="homestay-list">
                    <h2>รายการบ้านพัก</h2>
                    <div class="homestay-grid" id="homestay-container">

                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Info Modal -->
    <div id="infoModal" class="modal-info">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> ข้อมูลเพิ่มเติม</h2>
                <button class="close" onclick="closeInfoModal()">&times;</button>
            </div>
            <div class="modal-body">

                <div class="greeting">นโยบายการคืนเงิน</div>
                <p>รายการจองงที่ชำระผ่าน QR Code จะสามาถทำเรื่องขอเงินคืนออนไลน์ได้</p>
                <div id="policy">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal" onclick="closeInfoModal()">
                    <i class="fas fa-check"></i> เข้าใจแล้ว
                </button>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const filter = document.getElementById('filter');
            const productContainer = document.getElementById('homestay-container');

            function fetchHomestays(searchTerm = '') {
                fetch('../../controls/search_homestay.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'search_query': searchTerm
                    })
                })
                    .then(response => response.json())

                    .then(products => {

                        // เคลียร์ข้อมูลเก่าใน container
                        productContainer.innerHTML = '';

                        if (products.length > 0) {
                            // วนลูปสร้างการ์ดสินค้าจากข้อมูลที่ได้
                            products.forEach(product => {
                                const productCard = document.createElement('div');
                                productCard.className = 'homestay-card';
                                productCard.innerHTML =
                                    `<div class='homestay-info'>
                                <img src='../../public/${product.Property_image}' alt='${product.Property_image}'>
                                <h3>${product.Property_name}</h3>
                                <div >
                                <span style='color:#f5b301; font-size:1.1rem; margin-left:0.5rem;'>
                                <i class='fa-solid fa-star'> </i> ${Number(product.Rating, 1).toFixed(1)}
                                </span>
                                </div>
                                <p>${product.Host_firstname} ${product.Host_lastname}</p>
                                <p class='location'><i class='fa-solid fa-location-pin'></i>
                                    จ.${product.Property_province} อ.${product.Property_district} ต.${product.Property_subdistrict}
                                </p>
                                <form method='post' action='detail_house1.php' style='display:inline;'>
                                    <input type='hidden' name='house_id'
                                        value=${product.Property_id}>
                                    <button type='submit' class='book-btn'>
                                        <i class='fa-solid fa-house'></i> ดูรายละเอียด
                                    </button>
                                </form>
                                </div>`;
                                productContainer.appendChild(productCard);
                            });
                        } else {
                            productContainer.innerHTML = '<p>ไม่พบสินค้าที่ตรงกับคำค้นหา</p>';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
            searchBtn.addEventListener('click', function () {
                fetchHomestays(searchInput.value);
            });
            filter.addEventListener('change', function () {
                fetchHomestays(filter.value);
            })
            fetchHomestays();
        });
        const slides = document.querySelectorAll(".banner-slider img");
        const prevBtn = document.querySelector(".prev");
        const nextBtn = document.querySelector(".next");
        let index = 0;
        let interval = setInterval(nextSlide, 5000);

        function showSlide(i) {
            slides.forEach(slide => slide.classList.remove("active"));
            slides[i].classList.add("active");
        }

        function nextSlide() {
            index = (index + 1) % slides.length;
            showSlide(index);
        }

        function prevSlide() {
            index = (index - 1 + slides.length) % slides.length;
            showSlide(index);
        }

        nextBtn.addEventListener("click", () => {
            nextSlide();
            resetInterval();
        });

        prevBtn.addEventListener("click", () => {
            prevSlide();
            resetInterval();
        });

        function resetInterval() {
            clearInterval(interval);
            interval = setInterval(nextSlide, 8000);
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-with-sidebar');
            sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("sidebar-collapsed");
        }

        // Modal Functions
        async function showInfoModal() {
            try {
                const res = await fetch('../../api/get_policy.php');
                const policies = await res.json();
                const msg_policy = document.getElementById('policy');
                msg_policy.innerHTML = '';
                if (policies === 0) {
                    msg_policy.innerHTML = '<div class="message">ไม่พบนโยบายการคืนเงิน</div>';
                    return
                }
                policies.forEach(policy => {
                    const data = document.createElement('div');
                    data.innerHTML = `
                        <div class="message">${policy.Policy_description}</div>
                    `;
                    msg_policy.appendChild(data);
                });
                const modal = document.getElementById('infoModal');
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';

            } catch (error) {
                console.error('เกิดข้อผิดพลาดในข้อมููล:', error);
            }
        }

        function closeInfoModal() {
            const modal = document.getElementById('infoModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        // Close modal when clicking outside of it
        window.onclick = function (event) {
            const modal = document.getElementById('infoModal');
            if (event.target === modal) {
                closeInfoModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeInfoModal();
            }
        });
    </script>
</body>

</html>