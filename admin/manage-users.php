<?php
session_start();
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
    <title>Manage Users - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="../style/barStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .admin-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        background: #1e5470;
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        text-align: center;
    }

    .table-responsive {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 2rem;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th,
    .table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e5e5;
        text-align: left;
    }

    .table th {
        background: #f3f4f6;
        font-weight: 600;
    }

    .table tr:last-child td {
        border-bottom: none;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #666;
    }

    .empty-state i {
        font-size: 3rem;
        color: #e1e5e9;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: 1rem;
        }

        .table-responsive {
            padding: 1rem;
        }
    }

    .status-active {
        color: #10b981;
        font-weight: bold;
    }

    .status-inactive {
        color: #ef4444;
        font-weight: bold;
    }

    .btn-edit {
        background: #3b82f6;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 0.4rem 0.7rem;
        margin-right: 2px;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #2563eb;
    }

    .btn-delete {
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 0.4rem 0.7rem;
        margin-right: 2px;
        cursor: pointer;
    }

    .btn-delete:hover {
        background: #dc2626;
    }

    .btn-ban {
        background: #f59e42;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 0.4rem 0.7rem;
        margin-right: 2px;
        cursor: pointer;
    }

    .btn-ban:hover {
        background: #d97706;
    }

    .btn-unban {
        background: #10b981;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 0.4rem 0.7rem;
        margin-right: 2px;
        cursor: pointer;
    }

    .btn-unban:hover {
        background: #059669;
    }

    .phone-prefix-display {
        background: #879094ff;
        color: white;
        padding: 0.2rem 0.2rem;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.875rem;
        user-select: none;
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
                <li><a href="admin-dashboard.php" title="หน้าแดชบอร์ด"><i class="fas fa-tachometer-alt"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a></li>
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก"><i class="fas fa-check-circle"></i><span
                            class="menu-label">Approve Properties</span></a></li>
                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                            class="menu-label">Hosts</span></a></li>
                <li><a href="manage-users.php" class="active" title="จัดการผู้ใช้งาน"><i
                            class="fas fa-user-friends"></i><span class="menu-label">Users</span></a></li>
                <li><a href="manage-reviews.php" title="รีวิวจากผู้ใช้งาน"><i class="fas fa-star"></i><span
                            class="menu-label">Reviews</span></a></li>
                <li><a href="violations.php" title="รายการการละเมิด"><i class="fas fa-exclamation-triangle"></i><span
                            class="menu-label">Violations</span></a></li>
                <li><a href="../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
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
                    <h1><i class="fas fa-user-friends"></i> Manage Users</h1>
                    <p>จัดการข้อมูลผู้ใช้งานระบบทั้งหมด</p>
                </div>
                <div class="table-responsive">
                    <?php include "../controls/get-users.php"; ?>
                    <?php if (count($users) > 0): ?>
                    <?php echo "<h1>รายชื่อทั้งหมด (".count( $users) .")</h1>" ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ลำดับ</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>อีเมล</th>
                                <th>เบอร์โทร</th>
                                <th>สถานะ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['User_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['Firstname'] . ' ' . $user['Lastname']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['User_email']); ?></td>
                                <td><?php echo htmlspecialchars($user['Phone']) ; ?>
                                </td>
                                <td>
                                    <?php
                                        $status = ($user['User_Status'] == 0) ? "Active" : "Inactive";
                                        $statusClass = ($user['User_Status'] == 0) ? 'status-active' : 'status-inactive';
                                    ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <form method="post" action="edit-user.php" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['User_id']; ?>">
                                        <button type="submit" class="btn btn-edit" title="แก้ไข"><i
                                                class="fas fa-edit"></i></button>
                                    </form>
                                    <form method="post" action="delete-user.php" style="display:inline;"
                                        onsubmit="return confirm('คุณต้องการลบผู้ใช้นี้หรือไม่?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['User_id']; ?>">
                                        <button type="submit" class="btn btn-delete" title="ลบ"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                    <form method="post" action="toggle-ban-user.php" style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['User_id']; ?>">
                                        <?php if ($user['User_Status'] == 1): ?>
                                        <button type="submit" class="btn btn-ban" title="แบน"><i
                                                class="fas fa-ban"></i></button>
                                        <?php else: ?>
                                        <button type="submit" class="btn btn-unban" title="ปลดแบน"><i
                                                class="fas fa-undo"></i></button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-friends"></i>
                        <h3>ไม่มีข้อมูลผู้ใช้งาน</h3>
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
</body>

</html>