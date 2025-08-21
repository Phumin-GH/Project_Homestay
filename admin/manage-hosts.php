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
    <title>Manage Hosts - Homestay Booking</title>
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

    /* .empty-state {
        text-align: center;
        padding: 2rem;
        color: #666;
    }

    .empty-state i {
        font-size: 3rem;
        color: #e1e5e9;
        margin-bottom: 1rem;
    } */

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
        background: #1e5470;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 0.4rem 0.7rem;
        margin-right: 2px;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #29749aff;
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

    .btn-approve {
        background: #10b981;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 0.4rem 0.7rem;
        margin-right: 2px;
        cursor: pointer;
    }

    .btn-approve:hover {
        background: #059669;
    }

    .btn-reject {
        background: #ef4444;
        color: #fff;
        border: none;
        border-radius: 5px;
        padding: 0.4rem 0.7rem;
        margin-right: 2px;
        cursor: pointer;
    }

    .btn-reject:hover {
        background: #dc2626;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.4);
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        border-radius: 12px;
        max-width: 1200px;
        width: 100%;
        padding: 2rem;
        position: relative;
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.15);
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: #888;
        cursor: pointer;
    }

    .modal-header {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    .unauthorized-host {
        border-bottom: 1px solid #eee;
        padding: 1rem 0;
    }

    .unauthorized-host:last-child {
        border-bottom: none;
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

    .notify-display {
        background: #e32b06ff;
        /* สีแดง */
        width: 10px;
        height: 10px;
        border-radius: 50%;
        /* ทำให้เป็นวงกลม */
        display: inline-block;
        position: absolute;
        /* วางในมุมของ parent */
        animation: float 0.5s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: scale(1);
        }

        25% {
            transform: scale(1.2);
        }

        50% {
            transform: scale(1.3);
        }

        75% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
        }
    }

    /* ปิดการแสดงผลเมื่อไม่ต้องแสดง notify */
    .non-notify-display {
        display: none;
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

                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก" class="active"><i
                            class="fas fa-users"></i><span class="menu-label">Hosts</span></a></li>
                <li><a href="manage-users.php" title="จัดการผู้ใช้งาน"><i class="fas fa-user-friends"></i><span
                            class="menu-label">Users</span></a></li>
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
                    <h1><i class="fas fa-users"></i> Manage Hosts</h1>
                    <p>จัดการข้อมูลเจ้าของบ้านพักทั้งหมด</p>
                </div>
                <?php include "../controls/get_hosts.php"; ?>
                <button onclick="openWaitingHostsModal()"
                    style="margin-bottom: 1.5rem; background: #12690bff; color: #fff; gap: 500px;  border: none; border-radius: 5px; padding: 0.6rem 1.2rem; font-size: 1.25rem; cursor: pointer;">
                    <i class="fa-solid fa-user-plus"></i> บัญชีที่รออนุญาต
                    <?php echo "(". count($verify_host) .")"; ?>
                    <?php if (count($verify_host) > 0): ?>
                    <div class="notify-display"></div>
                    <?php else: ?>
                    <div class="non-notify-display"></div>
                    <?php endif; ?>
                </button>
                <button onclick="openUnauthorizedHostsModal()"
                    style="margin:0.5rem 0  1.5rem 0; background: #a0682cff; color: #fff; border: none; border-radius: 5px; padding: 0.6rem 1.2rem; font-size: 1.25rem; cursor: pointer;">
                    <i class="fa-solid fa-users-slash"></i> บัญชีที่ไม่อนุญาต
                    <?php echo "(". count($ban_host) .")"; ?>

                </button>
                <div class="table-responsive">

                    <?php if (count( $hosts) > 0): ?>
                    <?php echo "<h1>รายชื่อทั้งหมด (".count( $hosts) .")</h1>" ?>
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
                            <?php foreach ($hosts as $host): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($host['Host_id']); ?></td>
                                <td><?php echo htmlspecialchars($host['Host_firstname'] . ' ' . $host['Host_lastname']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($host['Host_email']); ?></td>
                                <td><?php echo "<span class='phone-prefix-display'>66+</span>" .'   '. htmlspecialchars($host['Host_phone']); ?>
                                </td>
                                <td>
                                    <?php
                                        $status = ($host['Host_Status'] == 1) ? "ใช้งานได้" : "ถูกระงับ";
                                        $statusClass = ($host['Host_Status'] == 1) ? 'status-active' : 'status-inactive';
                                    ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <form method="post" action="edit-host.php" style="display:inline;">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                        <button type="submit" class="btn btn-edit" title="แก้ไข"><i
                                                class="fas fa-edit"></i></button>
                                    </form>
                                    <form method="post" action="delete-host.php" style="display:inline;"
                                        onsubmit="return confirm('คุณต้องการลบเจ้าของบ้านพักนี้หรือไม่?');">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                        <button type="submit" class="btn btn-delete" title="ลบ"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                    <form method="post" action="toggle-approve-host.php" style="display:inline;">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                        <?php if ($host['Host_Status'] == 2): ?>
                                        <button type="submit" class="btn btn-reject" title="ไม่อนุมัติ"><i
                                                class="fas fa-times"></i></button>
                                        <?php else: ?>
                                        <button type="submit" class="btn btn-approve" title="อนุมัติ"><i
                                                class="fas fa-check"></i></button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>ไม่มีข้อมูลเจ้าของบ้านพัก</h3>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal for Unauthorized Hosts -->
    <div id="waitingHostsModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('waitingHostsModal').style.display='none'"></span>
            <h2>เจ้าของบ้านที่รอการอนุมัติ</h2>

            <?php include "../controls/get_hosts.php"; ?>
            <?php
if (!isset($verify_host)) $verify_host = [];
?>
            <?php if (count(value: $verify_host) > 0): ?>
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
                    <?php foreach ($verify_host as $host): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($host['Host_id']); ?></td>
                        <td><?php echo htmlspecialchars($host['Host_firstname'] . ' ' . $host['Host_lastname']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($host['Host_email']); ?></td>
                        <td><?php echo "<span class='phone-prefix-display'>66+</span>".'   '. htmlspecialchars($host['Host_phone']); ?>
                        </td>
                        <td>
                            <?php
                                        $status = ($host['Host_Status'] == 0) ? "รออนุมัติ" : "ไม่อนุมัติ";
                                        $statusClass = ($host['Host_Status'] == 0) ? 'status-active' : 'status-inactive';
                                    ?>
                            <span class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                        </td>
                        <td>
                            <form method="post" action="edit-host.php" style="display:inline;">
                                <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                <button type="submit" class="btn btn-edit" title="แก้ไข"><i
                                        class="fas fa-edit"></i></button>
                            </form>
                            <form method="post" action="delete-host.php" style="display:inline;"
                                onsubmit="return confirm('คุณต้องการลบเจ้าของบ้านพักนี้หรือไม่?');">
                                <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                <button type="submit" class="btn btn-delete" title="ลบ"><i
                                        class="fas fa-trash"></i></button>
                            </form>
                            <form method="post" action="toggle-approve-host.php" style="display:inline;">
                                <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                <?php if ($host['Host_Status'] == 1): ?>
                                <button type="submit" class="btn btn-reject" title="ไม่อนุมัติ"><i
                                        class="fas fa-times"></i></button>
                                <?php else: ?>
                                <button type="submit" class="btn btn-approve" title="อนุมัติ"><i
                                        class="fas fa-check"></i></button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>ไม่มีข้อมูลเจ้าของบ้านพัก</h3>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Unauthorized Hosts -->
    <div id="unauthorizedHostsModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('unauthorizedHostsModal').style.display='none'"></span>
            <h2>เจ้าของบ้านที่ยังไม่อนุมัติ</h2>

            <?php include "../controls/get_hosts.php"; ?>
            <?php
if (!isset($ban_host)) $ban_host = [];
?>
            <?php if (count(value: $ban_host) > 0): ?>
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
                    <?php foreach ($ban_host as $host): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($host['Host_id']); ?></td>
                        <td><?php echo htmlspecialchars($host['Host_firstname'] . ' ' . $host['Host_lastname']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($host['Host_email']); ?></td>
                        <td><?php echo "<span class='phone-prefix-display'>66+</span>".'   '. htmlspecialchars($host['Host_phone']); ?>
                        </td>
                        <td>
                            <?php
                                        $status = ($host['Host_Status'] == 0) ? "รออนุมัติ" : "ไม่อนุมัติ";
                                        $statusClass = ($host['Host_Status'] == 0) ? 'status-active' : 'status-inactive';
                                    ?>
                            <span class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                        </td>
                        <td>
                            <form method="post" action="edit-host.php" style="display:inline;">
                                <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                <button type="submit" class="btn btn-edit" title="แก้ไข"><i
                                        class="fas fa-edit"></i></button>
                            </form>
                            <form method="post" action="delete-host.php" style="display:inline;"
                                onsubmit="return confirm('คุณต้องการลบเจ้าของบ้านพักนี้หรือไม่?');">
                                <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                <button type="submit" class="btn btn-delete" title="ลบ"><i
                                        class="fas fa-trash"></i></button>
                            </form>
                            <form method="post" action="toggle-approve-host.php" style="display:inline;">
                                <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                <?php if ($host['Host_Status'] == 1): ?>
                                <button type="submit" class="btn btn-reject" title="ไม่อนุมัติ"><i
                                        class="fas fa-times"></i></button>
                                <?php else: ?>
                                <button type="submit" class="btn btn-approve" title="อนุมัติ"><i
                                        class="fa-solid fa-repeat"></i></i></button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>ไม่มีข้อมูลเจ้าของบ้านพัก</h3>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }

    function openUnauthorizedHostsModal() {
        document.getElementById('unauthorizedHostsModal').style.display = 'flex';
        // Load unauthorized hosts via AJAX
        const listDiv = document.getElementById('unauthorized-hosts-list');
        listDiv.innerHTML =
            '<div style="text-align:center;padding:2rem;"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>';
        fetch('get-unauthorized-hosts.php')
            .then(response => response.text())
            .then(html => {
                listDiv.innerHTML = html;
            })
            .catch(() => {
                listDiv.innerHTML =
                    '<div style="color:#dc2626;text-align:center;">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
            });
    }

    function openWaitingHostsModal() {
        document.getElementById('waitingHostsModal').style.display = 'flex';
        // Load unauthorized hosts via AJAX
        const listDiv = document.getElementById('unauthorized-hosts-list');
        listDiv.innerHTML =
            '<div style="text-align:center;padding:2rem;"><i class="fas fa-spinner fa-spin"></i> กำลังโหลด...</div>';
        fetch('get-unauthorized-hosts.php')
            .then(response => response.text())
            .then(html => {
                listDiv.innerHTML = html;
            })
            .catch(() => {
                listDiv.innerHTML =
                    '<div style="color:#dc2626;text-align:center;">เกิดข้อผิดพลาดในการโหลดข้อมูล</div>';
            });
    }

    function closeUnauthorizedHostsModal() {
        document.getElementById('unauthorizedHostsModal').style.display = 'none';
    }

    function closeWaitingHostsModal() {
        document.getElementById('waitingHostsModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('unauthorizedHostsModal');
        const modals = document.getElementById('waitingHostsModal');
        if (event.target === modal) {
            closeUnauthorizedHostsModal();
        } else if (event.target === modals) {
            closeWaitingHostsModal();
        }
    }
    </script>
</body>

</html>