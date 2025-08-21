<?php 
session_start();
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}

// Include database connection
/*include '../config/db_connect.php';

// Get host information
$host_email = $_SESSION['Host_email'];
$stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ?");
$stmt->execute([$host_email]);
$host = $stmt->fetch(PDO::FETCH_ASSOC);

// Get properties with approval status
$stmt = $conn->prepare("SELECT * FROM Property WHERE Host_id = ? ORDER BY Property_id DESC");
$stmt->execute([$host['Host_id']]);
$properties_result = $stmt;

// Get approved properties count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM Property WHERE Host_id = ? AND Property_status = 'approved'");
$stmt->execute([$host['Host_id']]);
$approved_properties = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Get pending properties count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM Property WHERE Host_id = ? AND Property_status = 'pending'");
$stmt->execute([$host['Host_id']]);
$pending_properties = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

// Get bookings for approved properties only
$stmt = $conn->prepare("
    SELECT b.*, p.Property_name, u.User_firstname, u.User_lastname, u.User_phone
    FROM bookings b
    JOIN Property p ON b.Property_id = p.Property_id
    JOIN users u ON b.User_email = u.User_email
    WHERE p.Host_id = ? AND p.Property_status = 'approved'
    ORDER BY b.Booking_date DESC
");
$stmt->execute([$host['Host_id']]);
$bookings_result = $stmt;

// Calculate total income from approved properties only
$stmt = $conn->prepare("
    SELECT SUM(Total_price) as total_income FROM bookings b
    JOIN Property p ON b.Property_id = p.Property_id
    WHERE p.Host_id = ? AND p.Property_status = 'approved'
");
$stmt->execute([$host['Host_id']]);
$total_income = $stmt->fetch(PDO::FETCH_ASSOC)['total_income'] ?? 0;

// Get reviews for approved properties only
$stmt = $conn->prepare("
    SELECT r.*, p.Property_name, u.User_firstname, u.User_lastname
    FROM reviews r
    JOIN Property p ON r.Property_id = p.Property_id
    JOIN users u ON r.User_email = u.User_email
    WHERE p.Host_id = ? AND p.Property_status = 'approved'
    ORDER BY r.Review_date DESC
");
$stmt->execute([$host['Host_id']]);
$reviews_result = $stmt;*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Dashboard - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .dashboard-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .sidebar.collapsed {
        width: 0;
        /* หรือซ่อน content ตามต้องการ */
        display: none;
        /* หรือ transform: translateX(-100%) */
    }

    .page-header {
        background: #1e5470;
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: #ffffff;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e5e5;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.5rem;
        color: white;
    }

    .stat-icon.properties {
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
    }

    .stat-icon.bookings {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .stat-icon.income {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .stat-icon.reviews {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #666;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .content-tabs {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e5e5;
        margin-bottom: 3rem;
    }

    .tab-nav {
        display: flex;
        border-bottom: 1px solid #e5e5e5;
        overflow-x: auto;
    }

    .tab-btn {
        padding: 1rem 2rem;
        background: none;
        border: none;
        cursor: pointer;
        font-weight: 500;
        color: #666;
        transition: all 0.2s ease;
        white-space: nowrap;
        border-bottom: 3px solid transparent;
    }

    .tab-btn.active {
        color: #1e5470;
        border-bottom-color: #4f46e5;
    }

    .tab-btn:hover {
        background: #f8f9ff;
    }

    .tab-content {
        padding: 2rem;
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .add-property-btn {
        background: #1e5470;
        color: white;
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }

    .add-property-btn:hover {
        background: #1e5470;
        transform: translateY(-1px);
    }

    .properties-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
    }

    .property-card {
        background: #ffffff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        border: 1px solid #e5e5e5;
        transition: all 0.3s ease;
    }

    .property-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .property-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .property-info {
        padding: 1.5rem;
    }

    .property-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.75rem;
    }

    .property-location {
        color: #666;
        font-size: 0.875rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .property-actions {
        display: flex;
        gap: 0.75rem;
    }

    .action-btn {
        flex: 1;
        padding: 0.5rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-align: center;
    }

    .edit-btn {
        background: #10b981;
        color: white;
    }

    .edit-btn:hover {
        background: #059669;
    }

    .delete-btn {
        background: #ff4757;
        color: white;
    }

    .delete-btn:hover {
        background: #ff3742;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #666;
    }

    .empty-state i {
        font-size: 4rem;
        color: #e1e5e9;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 1rem;
        }

        .page-header {
            padding: 2rem 1rem;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .tab-nav {
            flex-wrap: wrap;
        }

        .tab-btn {
            flex: 1;
            min-width: 120px;
        }

        .properties-grid {
            grid-template-columns: 1fr;
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
                <li><a href="host-dashboard.php" title="รายงาน" class="active"><i
                            class="fas fa-tachometer-alt"></i><span class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                            class="menu-label">Manage
                            Property</span></a></li>

                <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                            class="menu-label">Test</span></a></li>
                <li><a href="walkin-property.php" title="การจอง"><i class="fa-solid fa-person-walking"></i><span
                            class="menu-label">Walkin</span></a></li>
                <li><a href="../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul>

            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <div class="dashboard-container">
                <div class="page-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Host Dashboard</h1>
                    <p>Welcome back,
                        <?php echo htmlspecialchars($host['Host_firstname'] . ' ' . $host['Host_lastname']); ?>!</p>
                </div>

                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon properties">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-number"><?php echo $approved_properties; ?></div>
                        <div class="stat-label">Approved Properties</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bookings">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-number"><?php echo $bookings_result->rowCount(); ?></div>
                        <div class="stat-label">Total Bookings</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon income">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <div class="stat-number">฿<?php echo number_format($total_income); ?></div>
                        <div class="stat-label">Total Income</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon reviews">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-number"><?php echo $reviews_result->rowCount(); ?></div>
                        <div class="stat-label">Reviews</div>
                    </div>
                </div>

                <!-- Properties Section -->
                <div class="content-tabs">
                    <div class="section-title">
                        <i class="fas fa-home"></i>
                        My Properties
                    </div>
                    <a href="add-property.php" class="add-property-btn">
                        <i class="fas fa-plus"></i> Add New Property
                    </a>

                    <!-- Pending Properties -->
                    <?php if ($pending_properties > 0): ?>
                    <div style="margin-bottom: 2rem;">
                        <h3 style="color: #f59e0b; margin-bottom: 1rem;">
                            <i class="fas fa-clock"></i> รอการอนุมัติ (<?php echo $pending_properties; ?>)
                        </h3>
                        <div class="properties-grid">
                            <?php 
                            $stmt = $conn->prepare("SELECT * FROM Property WHERE Host_id = ? AND Property_status = 'pending' ORDER BY Property_id DESC");
                            $stmt->execute([$host['Host_id']]);
                            while ($property = $stmt->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                            <div class="property-card" style="border: 2px solid #f59e0b; opacity: 0.8;">
                                <img src="<?php echo htmlspecialchars($property['Property_image']); ?>"
                                    alt="<?php echo htmlspecialchars($property['Property_name']); ?>"
                                    class="property-image">
                                <div class="property-info">
                                    <h3 class="property-title">
                                        <?php echo htmlspecialchars($property['Property_name']); ?></h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        จ.<?php echo htmlspecialchars($property['Property_province']); ?>
                                        อ.<?php echo htmlspecialchars($property['Property_district']); ?>
                                    </div>
                                    <div
                                        style="background: #fff3cd; color: #856404; padding: 0.5rem; border-radius: 4px; margin: 0.5rem 0; font-size: 0.875rem;">
                                        <i class="fas fa-clock"></i> รอการอนุมัติจากผู้ดูแลระบบ
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Approved Properties -->
                    <?php if ($approved_properties > 0): ?>
                    <div>
                        <h3 style="color: #10b981; margin-bottom: 1rem;">
                            <i class="fas fa-check-circle"></i> อนุมัติแล้ว (<?php echo $approved_properties; ?>)
                        </h3>
                        <div class="properties-grid">
                            <?php 
                            $stmt = $conn->prepare("SELECT * FROM Property WHERE Host_id = ? AND Property_status = 'approved' ORDER BY Property_id DESC");
                            $stmt->execute([$host['Host_id']]);
                            while ($property = $stmt->fetch(PDO::FETCH_ASSOC)): 
                            ?>
                            <div class="property-card">
                                <img src="<?php echo htmlspecialchars($property['Property_image']); ?>"
                                    alt="<?php echo htmlspecialchars($property['Property_name']); ?>"
                                    class="property-image">
                                <div class="property-info">
                                    <h3 class="property-title">
                                        <?php echo htmlspecialchars($property['Property_name']); ?></h3>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        จ.<?php echo htmlspecialchars($property['Property_province']); ?>
                                        อ.<?php echo htmlspecialchars($property['Property_district']); ?>
                                    </div>
                                    <div
                                        style="background: #d4edda; color: #155724; padding: 0.5rem; border-radius: 4px; margin: 0.5rem 0; font-size: 0.875rem;">
                                        <i class="fas fa-check-circle"></i> อนุมัติแล้ว - สามารถรับการจองได้
                                    </div>
                                    <div class="property-actions">
                                        <a href="edit-property.php?id=<?php echo $property['Property_id']; ?>"
                                            class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="action-btn delete-btn"
                                            onclick="deleteProperty(<?php echo $property['Property_id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- No Properties -->
                    <?php if ($approved_properties == 0 && $pending_properties == 0): ?>
                    <div class="empty-state">
                        <i class="fas fa-home"></i>
                        <h3>No properties yet</h3>
                        <p>Start by adding your first property to begin receiving bookings.</p>
                        <a href="add-property.php" class="add-property-btn">Add Your First Property</a>
                    </div>
                    <?php endif; ?>
                </div>
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

    function deleteProperty(propertyId) {
        if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
            // Send AJAX request to delete property
            fetch('../controls/delete_property.php', {
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
                        alert('Error deleting property: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting property');
                });
        }
    }
    </script>
</body>

</html>