<?php 
session_start();
if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['User_email']);
    header("Location: ../index.php");
    exit();
}
require_once __DIR__ . '../config/db_connect.php';
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
    <link rel="stylesheet" href="style/style.css" />
    <link rel="stylesheet" href="style/main-menu.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>

    <header>
        <nav class="navbar">
            <div class="logo">
                <h1><img src="images/logo.png" alt="Logo" class="logo-images"
                        style="width: 3.5rem; height: 3.5rem;">Homestay bookings</h1>
            </div>
            <div class="menu">
                <ul>
                    <li><a href="#" class="active">Home</a></li>
                    <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li>

                    <li class="dropdown">
                        <button class="dropdown-toggle" id="dropdown-toggle" type="button">
                            <i class="fas fa-bars"></i>
                        </button>
                        <ul class="dropdown-menu" id="dropdown-menu">
                            <li><a href="hosts/host-dashboard.php"><i class="fas fa-home"></i>Become a host</a></li>
                            <li><a href="admin/admin-dashboard.php"><i class="fas fa-user-shield"></i>Log Admin</a></li>
                            <li><a href="users/user-login.php"><i
                                        class="fa-solid fa-right-to-bracket"></i>Login&Signup</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <main>
        <div class="banner-images">
            <img src="images/banner.jpg" alt="Banner">
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
                <?php
                        
                        foreach ($homestay as $house) {
                            echo "<div class='homestay-card'>";
                            echo "<div class='homestay-info'>";
                            echo "<img src='" . htmlspecialchars($house['Property_image']) . "' alt='" . htmlspecialchars($house['Property_name']) . "'><br>";
                            echo "<h3>". htmlspecialchars($house['Property_name']) . "</h3>";
                            echo "<button class='favorite-btn' onclick='favorite(". htmlspecialchars($house['Host_id']).")'><i class='fas fa-heart'></i></button>";
                            echo "<p>" . htmlspecialchars($house['Host_firstname']. ' '.$house['Host_lastname']) .  "</p>";
                            echo "<p class='location'><i class='fa-solid fa-location-pin'></i>".'  จ.' . htmlspecialchars($house['Property_province']. '  '. 'อ.'.$house['Property_district']. '  '. 'ต.'.$house['Property_subdistrict'] ). "<p>";
                            // echo "<a class='book-btn' href='detail-house.php?id=" . htmlspecialchars($house['Property_id']) . "'>Book Now</a>";
                            echo "<a class='book-btn' href='users/user-login.php'>Book Now</a>";
                            echo "</div></div>";
                        }
                    ?>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>



    <script src="script/Barscript.js"></script>

</body>

</html>