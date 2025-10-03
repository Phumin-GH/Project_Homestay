<?php
session_start();
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}
require_once __DIR__ . "/../../controls/log_hosts.php";
require_once __DIR__ . "/../../api/get_ListHomestay.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Dashboard - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 2rem;
            padding: 2rem;
        }

        .page-header {
            background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
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
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
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
            .admin-container {
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

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }

        .chart-container {
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .system-info {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
            padding: 2rem;
        }

        .system-info h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
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
        echo "<script>alert(" . json_encode($_SESSION['error']) . ");</script>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['message'])) {
        echo "<script>alert(" . json_encode($_SESSION['message']) . ");</script>";
        unset($_SESSION['message']);
    }
    ?>
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
                <li><a href="host-dashboard.php" title="รายงาน" class="active"><i
                            class="fa-solid fa-ranking-star"></i><span class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <?php if ($hosts['Host_Status'] == 'active'): ?>
                    <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
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
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <div class="admin-container">

                <div class="page-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Host Dashboard</h1>
                    <p>Welcome back,
                        <?php echo htmlspecialchars($hosts['Host_firstname'] . ' ' . $hosts['Host_lastname']); ?>!
                    </p>
                </div>
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon properties">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_properties; ?></div>
                        <div class="stat-label">Approved Properties</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bookings">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_bookings; ?></div>
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
                        <div class="stat-number"><?php echo $total_reviews; ?></div>
                        <div class="stat-label">Reviews</div>
                    </div>
                </div>
                <div class="system-info">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        System Information
                    </h3>
                    <div class="dashboard-grid">
                        <div class="chart-container">
                            <h2>ยอดขายรายเดือน</h2>
                            <canvas id="bookingStatsChart"></canvas>
                        </div>
                        <div class="chart-container">
                            <h2>ผู้สมัครใหม่ (30 วันล่าสุด)</h2>
                            <canvas id="propertyChart"></canvas>
                        </div>
                        <div class="chart-container">
                            <h2>ผู้สมัครใหม่ (30 วันล่าสุด)</h2>
                            <canvas id="hostRevenueChart"></canvas>
                        </div>
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
                                <?php foreach ($propertys as $property): ?>
                                    <div class="property-card" style="border: 2px solid #f59e0b; opacity: 0.8;">
                                        <img src="<?php echo htmlspecialchars($property['Property_image']); ?>"
                                            alt="<?php echo htmlspecialchars($property['Property_name']); ?>"
                                            class="property-image">
                                        <div class="property-info">
                                            <h3 class="property-title">
                                                <?php echo htmlspecialchars($property['Property_name']); ?>
                                            </h3>
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
                                <?php endforeach; ?>
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
                                                <?php echo htmlspecialchars($property['Property_name']); ?>
                                            </h3>
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
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- ✨ ฟังก์ชันสร้างกราฟยอดขาย (Bar Chart) ---
            async function renderhostRevenueChart() {
                const ctx = document.getElementById('hostRevenueChart');
                try {
                    const host_email = <?php echo json_encode($_SESSION['Host_email']) ?>;
                    const response = await fetch(
                        `../../api/report_host_rev.php?id=${host_email}&type=revenue`);
                    const revenueData = await response.json();

                    console.log(revenueData);
                    if (revenueData.error) throw new Error(revenueData.error);

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: revenueData.labels,
                            datasets: [{
                                    label: 'รายได้ ',
                                    data: revenueData.total_revenue,
                                    borderColor: 'rgba(54, 235, 123, 0.6)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: 'Platform',
                                    data: revenueData.total_revenue_plat,
                                    borderColor: 'rgba(172, 54, 235, 0.6)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }, {
                                    label: 'Host',
                                    data: revenueData.total_revenue_host,
                                    borderColor: 'rgba(54, 162, 235, 0.6)',
                                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error loading revenue chart:', error);
                    ctx.canvas.parentNode.innerHTML += '<p style="color:red;">ไม่สามารถโหลดข้อมูลรายได้ได้</p>';
                }
            }
            async function renderPropertyChart() {
                const ctx = document.getElementById('propertyChart');
                try {
                    const host_email = <?php echo json_encode($_SESSION['Host_email']) ?>;
                    const response = await fetch(
                        `../../api/report_host_rev.php?id=${host_email}&type=property`);
                    const revenueData = await response.json();

                    console.log(revenueData);
                    if (revenueData.error) throw new Error(revenueData.error);
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: revenueData.labels,
                            datasets: [{
                                    label: 'ยอดขาย (บาท)',
                                    data: revenueData.data,
                                    backgroundColor: 'rgba(8, 85, 136, 0.6)'
                                },

                            ],
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                } catch (error) {
                    console.error('Error loading revenue chart:', error);
                    ctx.canvas.parentNode.innerHTML += '<p style="color:red;">ไม่สามารถโหลดข้อมูลรายได้ได้</p>';
                }
            }
            async function renderBookingStatsChart() {
                const ctx = document.getElementById('bookingStatsChart');
                try {
                    const host_email = <?php echo json_encode($_SESSION['Host_email']) ?>;
                    const response = await fetch(
                        `../../api/report_host_rev.php?id=${host_email}&type=booking`
                    );
                    const statsData = await response.json();
                    // const statsData = await response.text();
                    console.log(statsData);
                    if (statsData.error) throw new Error(statsData.error);
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: statsData.labels,
                            datasets: [{
                                    label: 'ยอดขาย (บาท)',
                                    data: statsData.number,
                                    backgroundColor: 'rgba(8, 85, 136, 0.6)'
                                },
                                {
                                    label: 'Platfrom',
                                    data: statsData.income,
                                    backgroundColor: 'rgba(54, 162, 235, 0.6)',

                                },
                                {
                                    label: 'Host',
                                    data: statsData.refunds,
                                    backgroundColor: 'rgba(26, 116, 176, 0.6)'
                                },
                            ]
                        },
                        options: {
                            responsive: true
                        }
                    });
                } catch (error) {
                    console.error('Error loading stats chart:', error);
                    ctx.canvas.parentNode.innerHTML += '<p style="color:red;">ไม่สามารถโหลดข้อมูลสถิติได้</p>';
                }
            }
            renderhostRevenueChart();
            renderBookingStatsChart();
            renderPropertyChart();
        });
    </script>
</body>

</html>