<?php 
session_start();
if (!isset($_SESSION["User_email"])) {
    header("Location: ../index.php");
    exit();
}
include '../config/db_connect.php';
$email = $_SESSION["User_email"];
$stmt = $conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
$stmt->execute([$email]);
$user_id = $stmt->fetchColumn();

$select = $conn->prepare("SELECT f.Favorite_id, h.Host_firstname, h.Host_lastname, h.Host_phone,p.Property_id, p.Property_image, p.Property_name, p.Property_province, p.Property_district, p.Property_subdistrict 
FROM favorite f
INNER JOIN property p ON f.Property_id = p.Property_id
INNER JOIN host h ON p.Host_id = h.Host_id
WHERE User_id = ?");
$select->execute([$user_id]);
$favorites = $select->fetchAll(PDO::FETCH_ASSOC);

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

    /* --- Favorites Grid --- */
    .favorites-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .favorite-card {
        background: var(--card-background);
        border-radius: 8px;
        border: 1px solid var(--border-color);
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .favorite-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .favorite-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .favorite-info {
        padding: 1.25rem;
    }

    .favorite-info h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .favorite-info .host-name,
    .favorite-info .location {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .favorite-info i {
        font-size: 1.1rem;
    }

    .location {
        margin-bottom: 1.25rem;
    }

    .favorite-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
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
        background-color: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
    }

    .btn-danger {
        background-color: transparent;
        color: var(--danger-color);
        border: 1px solid var(--border-color);
        width: 2.25rem;
        height: 2.25rem;
        padding: 0;
        font-size: 1.1rem;
    }

    .btn-danger:hover {
        background-color: var(--danger-color);
        color: white;
        border-color: var(--danger-color);
    }

    /* --- Empty State --- */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: var(--card-background);
        border-radius: 8px;
        border: 1px dashed var(--border-color);
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--border-color);
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 2rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

    /* --- Responsive Design --- */
    @media (max-width: 768px) {
        .sidebar {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar .menu-label,
        .sidebar .logo h1,
        .sidebar-footer div span {
            display: none;
        }

        .main-with-sidebar {
            margin-left: var(--sidebar-collapsed-width);
            padding: 1rem;
            margin: 0 5rem;
        }

        .page-header h1 {
            font-size: 1.8rem;
        }

        .favorites-grid {
            grid-template-columns: 1fr;
        }
    }

    :root {
        --primary-color: #007bff;
        --primary-hover: #0056b3;
        --danger-color: #e74c3c;
        --danger-hover: #c0392b;
        --background-color: #f8f9fa;
        --card-background: #ffffff;
        --text-primary: #212529;
        --text-secondary: #6c757d;
        --border-color: #dee2e6;
        --sidebar-width: 240px;
        --sidebar-collapsed-width: 60px;
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
                <li><a href="bookings.php"><i class="fas fa-calendar"></i><span class="menu-label"
                            title="รายการจอง">Bookings</span></a>
                </li>
                <li><a href="reviews.php" title="รีวิวสถานที่พัก"><i class="fas fa-star"></i><span
                            class="menu-label">Review</span></a></li>
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

        <main class="main-with-sidebar" id="main-content">
            <div class="page-header">
                <h1><i class="ph-fill ph-heart"></i>My Favorites</h1>
                <p>Your saved homestays for future bookings.</p>
            </div>

            <?php if (count($favorites) > 0): ?>
            <h2>รายการโปรดทั้งหมด ( <?php echo count($favorites) ?> )</h2>
            <div class="favorites-grid">
                <?php foreach($favorites as $favorite): ?>
                <div class="favorite-card" data-property-id="<?php echo htmlspecialchars($favorite['Property_id']); ?>">
                    <img src="../<?php echo htmlspecialchars($favorite['Property_image']); ?>"
                        alt="<?php echo htmlspecialchars($favorite['Property_name']); ?>">
                    <div class="favorite-info">
                        <h3><?php echo htmlspecialchars($favorite['Property_name']); ?></h3>
                        <div class="host-name">
                            <i class="fa-solid fa-circle-user"></i>
                            <?php echo htmlspecialchars($favorite['Host_firstname'] . ' ' . $favorite['Host_lastname']); ?>
                        </div>
                        <div class="location">
                            <i class="fa-solid fa-location-pin"></i>
                            <span>
                                จ.<?php echo htmlspecialchars($favorite['Property_province']); ?>
                                อ.<?php echo htmlspecialchars($favorite['Property_district']); ?>
                                ต.<?php echo htmlspecialchars($favorite['Property_subdistrict']); ?>
                            </span>
                        </div>
                        <div class="favorite-actions">

                            <button class="btn-primary" data-action="delete">Book Now</button>
                            <button class="btn-danger">
                                <i class="fa-solid fa-heart-crack"></i>
                            </button>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="ph ph-heart-break"></i>
                <h3>No Favorites Yet</h3>
                <p>Start exploring and save your favorite homestays to see them here.</p>
                <a href="main-menu.php" class="btn btn-primary">Browse Homestays</a>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const favorite = document.querySelectorAll('.favorite-card');
        favorite.forEach(item => {
            const bookButton = item.querySelector('.btn-primary');
            const removeButton = item.querySelector('.btn-danger');
            removeButton.addEventListener('click', async () => {
                const propertyId = item.dataset.propertyId;
                alert(propertyId);
                const action = item.dataset.action;
                fetch("../controls/favorite.php", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            property_id: propertyId,
                            action: 'delete'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(data.message);
                            location.reload(); // Refresh the page to reflect changes
                        } else {
                            console.error("เกิดข้อผิดพลาด:", data.message);
                        }
                    })
                    .catch(error => console.error(error));

            });
            bookButton.addEventListener('click', async () => {
                const propertyId = item.dataset.propertyId;
                fetch("../controls/favorite.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                            property_id: propertyId,
                            submit: true
                        })
                        .then(response => response.json())

                });

            });
        });

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-with-sidebar');
            sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("sidebar-collapsed");
        }
        // function removeFavorite(propertyId) {
        //     if (confirm('Are you sure you want to remove this from favorites?')) {
        //         // Send AJAX request to remove favorite
        //         fetch('../controls/remove_favorite.php', {
        //                 method: 'POST',
        //                 headers: {
        //                     'Content-Type': 'application/json',
        //                 },
        //                 body: JSON.stringify({
        //                     property_id: propertyId
        //                 })
        //             })
        //             .then(response => response.json())
        //             .then(data => {
        //                 if (data.success) {
        //                     location.reload();
        //                 } else {
        //                     alert('Error removing favorite: ' + data.message);
        //                 }
        //             })
        //             .catch(error => {
        //                 console.error('Error:', error);
        //                 alert('Error removing favorite');
        //             });
        //     }
        //}
    });
    </script>
</body>

</html>