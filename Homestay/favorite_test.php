<?php 
session_start();
if (!isset($_SESSION["User_email"])) {
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .favorites-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 16px;
        margin-bottom: 3rem;
        text-align: center;
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

    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .favorite-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e5e5;
        transition: all 0.3s ease;
        position: relative;
    }

    .favorite-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .favorite-card img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .favorite-card:hover img {
        transform: scale(1.05);
    }

    .favorite-info {
        padding: 1.5rem;
        position: relative;
    }

    .favorite-info h3 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #1a1a1a;
        line-height: 1.4;
    }

    .favorite-info .host-name {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .favorite-info .location {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #666;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
    }

    .favorite-info .location i {
        color: #4f46e5;
    }

    .favorite-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .book-btn {
        flex: 1;
        background: #4f46e5;
        color: white;
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        text-align: center;
    }

    .book-btn:hover {
        background: #4338ca;
        transform: translateY(-1px);
    }

    .remove-favorite-btn {
        background: #ff4757;
        color: white;
        border: none;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .remove-favorite-btn:hover {
        background: #ff3742;
        transform: scale(1.1);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
    }

    .empty-state i {
        font-size: 4rem;
        color: #e1e5e9;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 2rem;
    }

    .browse-btn {
        background: #4f46e5;
        color: white;
        text-decoration: none;
        padding: 0.875rem 2rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .browse-btn:hover {
        background: #4338ca;
        transform: translateY(-1px);
    }

    @media (max-width: 768px) {
        .favorites-container {
            padding: 1rem;
        }

        .page-header {
            padding: 2rem 1rem;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .favorites-grid {
            grid-template-columns: 1fr;
            gap: 1.5rem;
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
                <li><a href="main-menu.php"><i class="fas fa-home"></i><span class="menu-label">Home</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i><span class="menu-label">Profile</span></a></li>
                <li><a href="favorites.php" class="active"><i class="fas fa-heart"></i><span
                            class="menu-label">Favorite</span></a>
                </li>
                <li><a href="bookings.php"><i class="fas fa-star"></i><span class="menu-label">Review</span></a></li>
                <li><a href="../controls/logout.php"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['User_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <div class="favorites-container">
                <div class="page-header">
                    <h1><i class="fas fa-heart"></i> My Favorites</h1>
                    <p>Your saved homestays for future bookings</p>
                </div>

                <?php if ($favorites_result->num_rows > 0): ?>
                <div class="favorites-grid">
                    <?php while ($favorite = $favorites_result->fetch_assoc()): ?>
                    <div class="favorite-card">
                        <img src="<?php echo htmlspecialchars($favorite['Property_image']); ?>"
                            alt="<?php echo htmlspecialchars($favorite['Property_name']); ?>">
                        <div class="favorite-info">
                            <h3><?php echo htmlspecialchars($favorite['Property_name']); ?></h3>
                            <div class="host-name">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($favorite['Host_firstname'] . ' ' . $favorite['Host_lastname']); ?>
                            </div>
                            <div class="location">
                                <i class="fas fa-map-marker-alt"></i>
                                จ.<?php echo htmlspecialchars($favorite['Property_province']); ?>
                                อ.<?php echo htmlspecialchars($favorite['Property_district']); ?>
                                ต.<?php echo htmlspecialchars($favorite['Property_subdistrict']); ?>
                            </div>
                            <div class="favorite-actions">
                                <a href="detail.php?id=<?php echo htmlspecialchars($favorite['Property_id']); ?>"
                                    class="book-btn">Book Now</a>
                                <button class="remove-favorite-btn"
                                    onclick="removeFavorite(<?php echo $favorite['Property_id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-heart-broken"></i>
                    <h3>No favorites yet</h3>
                    <p>Start exploring homestays and add them to your favorites for easy access later.</p>
                    <a href="main-menu.php" class="browse-btn">Browse Homestays</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }

    function removeFavorite(propertyId) {
        if (confirm('Are you sure you want to remove this from favorites?')) {
            // Send AJAX request to remove favorite
            fetch('../controls/remove_favorite.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        property_id: propertyId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error removing favorite: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error removing favorite');
                });
        }
    }
    </script>
</body>

</html>