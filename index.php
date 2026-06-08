<?php
session_start();
// if (isset($_GET['logout'])) {
//     session_destroy();
//     unset($_SESSION['User_email']);
//     header("Location: ../index.php");
//     exit();
// }
require_once __DIR__ . "/api/get_ListHomestay.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="images/jpg" href="public/images/logo.png">
    <title>Homestay Booking</title>
    <link rel="stylesheet" href="public/css/style.css" />
    <link rel="stylesheet" href="public/css/main-menu.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        .banner-slider {
            width: 100%;
            margin: 80px 0;
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
            background: linear-gradient(90deg,
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
                <h1><img src="public/images/logo.png" alt="Logo" class="logo-images"
                        style="width: 3.5rem; height: 3.5rem;">Homestay bookings</h1>
            </div>
            <div class="menu">
                <ul>
                    <li><a id="scollHome" class="active">หน้าหลัก</a></li>
                    <li><a id="scrollToReviewsBtn">เกี่ยวกับ</a></li>
                    <!-- <li><a href="#">Contact</a></li> -->

                    <li class="dropdown">
                        <button class="dropdown-toggle" id="dropdown-toggle" type="button">
                            <i class="fas fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu" id="dropdown-menu">
                            <li><a href="views/hosts/host-dashboard.php"><i
                                        class="fas fa-home"></i>เข้าสู่ระบบเจ้าของบ้าน</a>
                            </li>
                            <li><a href="views/admin/admin-dashboard.php"><i class="fas fa-user-shield"></i>แอดมิน</a>
                            </li>
                            <li><a href="views/users/user-login.php"><i
                                        class="fa-solid fa-right-to-bracket"></i>เข้าสู่ระบบผู้ใช้</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main>
        <div class="banner-slider" id="image_banner">
            <img src="public/images/logo/banner1.jpg" class="active" alt="Banner">
            <img src="public/images/logo/banner2.jpg" alt="Banner">
            <img src="public/images/logo/banner3.jpg" alt="Banner">
            <img src="public/images/logo/banner4.jpg" alt="Banner">
            <img src="public/images/logo/banner5.jpg" alt="Banner">
            <img src="public/images/logo/banner6.jpeg" alt="Banner">

            <div class="banner-text">
                <h2 class="btn-shine">ยินดีต้อนรับ</h2>
                <p class="btn-shine">ค้นหาการพักผ่อนที่สมบูรณ์แบบของคุณ</p>
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

    <footer id="reviews-container">
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>



    <script src=" public/js/Barscript.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');
            const filter = document.getElementById('filter');
            const productContainer = document.getElementById('homestay-container');

            function fetchHomestays(searchTerm = '') {
                fetch('controls/search_homestay.php', {
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
                        productContainer.innerHTML = '';
                        if (products.length > 0) {
                            products.forEach(product => {
                                const productCard = document.createElement('div');
                                productCard.className = 'homestay-card';
                                productCard.innerHTML = `
                                <div class='homestay-info'>
                                <img src='public/${product.Property_image}' alt='${product.Property_image}'>
                                <h3>${product.Property_name}</h3>
                                <div style='display:flex;gap:5px;'>
                                <span style='color:#f5b301; font-size:1.1rem; margin-left:0.5rem;'>
                                <i class='fa-solid fa-star'> </i> ${Number(product.Rating, 1).toFixed(1)} 
                                </span>
                                <div style= font-size:1.1rem; margin-left:0.5rem;'> (${product.review_count})</div>
                                </div>
                                <p>${product.Host_firstname} ${product.Host_lastname}</p>
                                <p class='location'><i class='fa-solid fa-location-pin'></i>
                                    จ.${product.Property_province} อ.${product.Property_district} ต.${product.Property_subdistrict}
                                </p>
                                <button class='book-btn' onclick="window.location.href='views/users/user-login.php'"> <i class='fa-solid fa-book'></i>  จอง</button>
                                </div>`;
                                productContainer.appendChild(productCard);
                            });
                        } else {
                            productContainer.innerHTML = '<p>ไม่พบสินค้าที่ตรงกับคำค้นหา</p>';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
            searchBtn.addEventListener('click', function() {
                fetchHomestays(searchInput.value);
            });
            filter.addEventListener('change', function() {
                fetchHomestays(filter.value);
            })
            fetchHomestays();
            const scrollButton = document.getElementById('scrollToReviewsBtn');
            const targetDiv = document.getElementById('reviews-container');
            const img = document.getElementById('image_banner');
            const scoll = document.getElementById('scollHome');
            scrollButton.onclick = function() {

                targetDiv.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            };
            scoll.addEventListener('click', function() {
                img.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                })
            });
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
    </script>
</body>

</html>