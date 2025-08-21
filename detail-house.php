<?php 
session_start();
include __DIR__ . '/config/db_connect.php';

// Get Property_id from URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$property_id) {
    echo "<h2>Invalid property ID.</h2>";
    exit();
}

// Fetch property details
$stmt = $conn->prepare("SELECT * FROM Property INNER JOIN host ON Property.Host_id = host.Host_id WHERE Property.Property_id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$property) {
    echo "<h2>Property not found.</h2>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" type="images/jpg" href="images/logo1.png">
    <title>House Details - <?php echo htmlspecialchars($property['Property_name']); ?></title>
    <link rel="stylesheet" href="style/style.css" />
    <link rel="stylesheet" href="style/main-menu.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    .detail-banner {
        width: 100%;
        max-height: 350px;
        object-fit: cover;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: 1px solid #e5e5e5;
    }
    .property-detail-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 2px 8px #ccc;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    .property-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    .property-meta {
        color: #666;
        margin-bottom: 1rem;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-top: 2rem;
        margin-bottom: 1rem;
        color: #1e5470;
    }
    .room-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
    }
    .room-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid #e5e5e5;
    }
    .calendar-placeholder, .booking-summary-placeholder {
        background: #f8f9fa;
        border: 1px dashed #bbb;
        border-radius: 10px;
        padding: 2rem;
        text-align: center;
        color: #888;
        margin-bottom: 2rem;
    }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1>
                    <img src="images/logo.png" alt="Logo" class="logo-image" style="width: 3.5rem; height: 3.5rem;">
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
                <li><a href="index.php"><i class="fas fa-home"></i><span class="menu-label">Home</span></a></li>
                <li><a href="users/profile.php"><i class="fas fa-user"></i><span class="menu-label">Profile</span></a></li>
                <li><a href="users/favorites.php"><i class="fas fa-heart"></i><span class="menu-label">Favorite</span></a></li>
                <li><a href="users/bookings.php"><i class="fas fa-star"></i><span class="menu-label">Booking</span></a></li>
                <li><a href="controls/logout.php"><i class="fas fa-sign-out-alt"></i><span class="menu-label">Logout</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo isset($_SESSION['User_email']) ? htmlspecialchars($_SESSION['User_email']) : 'Guest'; ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <main>
                <img class="detail-banner" src="<?php echo '../' . htmlspecialchars($property['Property_image']); ?>" alt="<?php echo htmlspecialchars($property['Property_name']); ?>">
                <div class="property-detail-card">
                    <div class="property-title"><?php echo htmlspecialchars($property['Property_name']); ?></div>
                    <div class="property-meta">
                        <i class="fas fa-map-marker-alt"></i> จ.<?php echo htmlspecialchars($property['Property_province']); ?> อ.<?php echo htmlspecialchars($property['Property_district']); ?> ต.<?php echo htmlspecialchars($property['Property_subdistrict']); ?>
                        <br>
                        <i class="fas fa-user"></i> Host: <?php echo htmlspecialchars($property['Host_firstname'] . ' ' . $property['Host_lastname']); ?>
                        <br>
                        <b>Status:</b> <?php echo htmlspecialchars($property['Property_status']); ?>
                    </div>
                    <div class="section-title">Room Details</div>
                    <div class="room-list">
                        <div class="room-card">Room details placeholder</div>
                        <!-- Add PHP loop for real room data here -->
                    </div>
                    <div class="section-title">Availability Calendar</div>
                    <div class="calendar-placeholder">[Calendar will be shown here]</div>
                    <div class="section-title">Booking Summary</div>
                    <div class="booking-summary-placeholder">[Booking summary will be shown here]</div>
                </div>
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