<?php 
session_start();
if (isset($_SESSION["User_email"])) {
    
    $user_email = $_SESSION["User_email"];
    $_SESSION['msg']='login first';
} else {
    
    header("Location: ../index.php");
    exit();
}

include __DIR__ . '/../config/db_connect.php';


    // guest เห็น property ที่อนุมัติแล้ว
    $stmt = $conn->prepare("SELECT * FROM Property INNER JOIN Host on Property.Host_id = Host.Host_id WHERE Property.Property_status = 1");
    $stmt->execute();
    $homestay = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="images/jpg" href="images/logo1.png">
    <title>Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css" />
    <link rel="stylesheet" href="../style/main-menu.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>

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
                <li><a href="../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
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
                <div class="banner-image">
                    <img src="../images/banner.jpg" alt="Banner">
                    <div class="banner-text">
                        <h2 class="btn-shine">Welcome to Homestay Booking</h2>
                        <p class="btn-shine">Find your perfect getaway</p>
                    </div>
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
                                <img src="../<?php echo htmlspecialchars($house["Property_image"]);?>"
                                    alt="<?php echo htmlspecialchars($house["Property_name"]) ?>"><br>
                                <h3><?php echo htmlspecialchars($house["Property_name"]); ?></h3>
                                <!-- <button class="favorite-btn" onclick="favorite(<?php echo (int)$house['Host_id']; ?>)">
                                    <i class="fas fa-heart"></i>
                                </button> -->

                                <p> <?php echo htmlspecialchars($house["Host_firstname"]. " " .$house["Host_lastname"])?>
                                </p>
                                <p class="location"><i class="fa-solid fa-location-pin"></i>
                                    จ.<?php echo htmlspecialchars($house["Property_province"]);?>
                                    อ.<?php echo htmlspecialchars($house["Property_district"]); ?><br>
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
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }
    </script>
</body>

</html>