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
            margin-bottom: 2.5rem;
            height: 550px;
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
            width: 80%;
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
                <li><a href="main-menu.php" class="active" title="หน้าแดชบอร์ด"><i class="fas fa-home"></i><span
                            class="menu-label">Home</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a></li>

                <li><a href="favorites.php" title="รายการสถานที่พักที่ถูกใจ"><i class="fas fa-heart"></i><span
                            class="menu-label">Favorite</span></a>
                </li>
                <li><a href="bookings.php"><i class="fas fa-calendar"></i><span class="menu-label"
                            title="รายการจอง">Bookings</span></a>
                </li>
                <li><a href="reviewa.php" title="รีวิวสถานที่พัก"><i class="fas fa-star"></i><span
                            class="menu-label">Review</span></a></li>
                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a>
                </li>
            </ul>


            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"
                        title="lllll"><?php echo htmlspecialchars($_SESSION['User_email']); ?></span>
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
                        <h2 class="btn-shine">Welcome to Homestay Booking</h2>
                        <p class="btn-shine">Find your perfect getaway</p>
                    </div>

                    <button class="slider-btn prev">&#10094;</button>
                    <button class="slider-btn next">&#10095;</button>
                </div>

                <section class="search-section">
                    <div class="search-container">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Search homestays..." />
                            <input type="date" id="checkInDate" />
                            <input type="date" id="checkOutDate" />
                            <button type="button"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </section>

                <section class="homestay-list">
                    <h2>Available Homestays</h2>
                    <div class="homestay-grid">
                        <!-- include '../controls/get_homestay.php';  -->
                        <?php foreach ($homestay as $house): ?>
                            <div class="homestay-card">
                                <div class="homestay-info">
                                    <img src="../../public/<?php echo htmlspecialchars($house["Property_image"]); ?>"
                                        alt="<?php echo htmlspecialchars($house["Property_name"]) ?>"><br>
                                    <h3><?php echo htmlspecialchars($house["Property_name"]); ?></h3>
                                    <!-- <button class="favorite-btn" onclick="favorite(<?php echo (int)$house['Host_id']; ?>)">
                                    <i class="fas fa-heart"></i>
                                </button> -->

                                    <p> <?php echo htmlspecialchars($house["Host_firstname"] . " " . $house["Host_lastname"]) ?>
                                    </p>
                                    <p class="location">คะแนนรีวิว</p>
                                    <p class="location"><i class="fa-solid fa-location-pin"></i>
                                        จ.<?php echo htmlspecialchars($house["Property_province"]); ?>
                                        อ.<?php echo htmlspecialchars($house["Property_district"]); ?>
                                        ต.<?php echo htmlspecialchars($house["Property_subdistrict"]); ?>
                                    <p>

                                    <form method="post" action="detail_house.php" style="display:inline;">
                                        <input type="hidden" name="house_id"
                                            value="<?php echo htmlspecialchars($house["Property_id"]); ?>">
                                        <button type="submit" class="book-btn">
                                            <i class="fa-solid fa-house"></i> ดูรายละเอียด
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>

    <script>
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
    </script>
</body>

</html>