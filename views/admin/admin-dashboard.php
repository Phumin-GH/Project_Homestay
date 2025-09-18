<?php
session_start();
include_once __DIR__ . '/../../controls/log_admin.php';
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="../../public/css/barStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
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
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: #ffffff;
            padding: 1.5rem;
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
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
            color: white;
        }

        .stat-icon.properties {
            background: #1e5470;
        }

        .stat-icon.hosts {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .stat-icon.users {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .stat-icon.bookings {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .stat-icon.income {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        }

        .stat-icon.reviews {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #666;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .action-card {
            background: #ffffff;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .action-icon {
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

        .action-icon.manage-properties {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }

        .action-icon.manage-hosts {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .action-icon.manage-users {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .action-icon.manage-reviews {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .action-icon.reports {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .action-icon.violations {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
        }

        .action-icon.approve-properties {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .action-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .action-description {
            color: #666;
            font-size: 0.875rem;
            line-height: 1.5;
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .info-item {
            text-align: center;
            padding: 1rem;
            background: #f8f9ff;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
        }

        .info-label {
            font-size: 0.875rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
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

            .quick-actions {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
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

    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <button class="toggle-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <ul class="sidebar-menu">
                <li><a href="admin-dashboard.php" title="หน้าแดชบอร์ด" class="active"><i
                            class="fas fa-tachometer-alt"></i><span class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a></li>
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก"><i class="fas fa-check-circle"></i><span
                            class="menu-label">Approve Properties</span></a></li>
                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                            class="menu-label">Hosts</span></a></li>
                <li><a href="manage-users.php" title="จัดการผู้ใช้งาน"><i class="fas fa-user-friends"></i><span
                            class="menu-label">Users</span></a></li>
                <li><a href="manage-reviews.php" title="รีวิวจากผู้ใช้งาน"><i class="fas fa-star"></i><span
                            class="menu-label">Reviews</span></a></li>
                <li><a href="violations.php" title="รายการการละเมิด"><i class="fas fa-exclamation-triangle"></i><span
                            class="menu-label">Violations</span></a></li>
                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Admin_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <div class="admin-container">
                <div class="page-header">
                    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
                    <p>Welcome back,
                        <?php echo htmlspecialchars($admin['Admin_username']); ?> !</p>
                </div>
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon properties">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_properties); ?></div>
                        <div class="stat-label">Properties</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon hosts">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_hosts); ?></div>
                        <div class="stat-label">Hosts</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon users">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_users); ?></div>
                        <div class="stat-label">Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon bookings">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_bookings); ?></div>
                        <div class="stat-label">Bookings</div>
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
                        <div class="stat-number"><?php echo number_format($total_reviews); ?></div>
                        <div class="stat-label">Reviews</div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="approve-properties.php" class="action-card">
                        <div class="action-icon approve-properties">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="action-title">Approve Properties</div>
                        <div class="action-description">Review and approve pending property registrations</div>
                    </a>
                    <a href="manage-properties.php" class="action-card">
                        <div class="action-icon manage-properties">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="action-title">Manage Properties</div>
                        <div class="action-description">View, edit, and manage all registered properties</div>
                    </a>
                    <a href="manage-hosts.php" class="action-card">
                        <div class="action-icon manage-hosts">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="action-title">Manage Hosts</div>
                        <div class="action-description">Manage host accounts and property registrations</div>
                    </a>
                    <a href="manage-users.php" class="action-card">
                        <div class="action-icon manage-users">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="action-title">Manage Users</div>
                        <div class="action-description">View and manage user accounts and activities</div>
                    </a>
                    <a href="manage-reviews.php" class="action-card">
                        <div class="action-icon manage-reviews">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="action-title">Manage Reviews</div>
                        <div class="action-description">Monitor and moderate guest reviews</div>
                    </a>
                    <a href="violations.php" class="action-card">
                        <div class="action-icon violations">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="action-title">Violation Reports</div>
                        <div class="action-description">Handle user violations and complaints</div>
                    </a>
                    <a href="reports.php" class="action-card">
                        <div class="action-icon reports">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <div class="action-title">System Reports</div>
                        <div class="action-description">Generate reports and view system analytics</div>
                    </a>
                </div>

                <!-- System Information -->
                <div class="system-info">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        System Information
                    </h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">PHP Version</div>
                            <div class="info-value"><?php echo PHP_VERSION; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Server Time</div>
                            <div class="info-value"><?php echo date('H:i:s'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Server Date</div>
                            <div class="info-value"><?php echo date('d/m/Y'); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Database</div>
                            <div class="info-value">MySQL</div>
                        </div>
                    </div>
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
</body>

</html>