<?php
session_start();
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}
// include "../controls/get_datas.php";
require_once __DIR__ . '/../../api/get_ListVerify.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hosts - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="../../public/css/barStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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

    .status-pending {
        color: #0798f8ff;
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

    .modal-input {
        width: 100%;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .modal-input:focus {
        border-color: #4A90E2;
        box-shadow: 0 0 6px rgba(74, 144, 226, 0.4);
        outline: none;
    }

    .auth-tabs {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e5e5e5;
        margin: 1.5rem 0;
    }

    .tab {
        flex: 1;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        font-weight: 500;
        color: #666666;
        transition: all 0.2s ease;
        position: relative;
        filter: blur(1px);
        opacity: 0.9;
        transform: translateY(10px);

        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    .tab:hover {
        filter: none;
        opacity: 1;
        color: #1e5470;
    }

    .tab.active {
        color: #1e5470;
        background: #ddf1faff;
        filter: none;
        opacity: 1;
        border-radius: 15px 30px 0 0;
        transform: translateY(0);
    }

    .tab.active::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: #1e5470;
    }

    .UserH2 {
        font-size: 1.5rem;
        font-weight: bold;
        margin: 1rem 0 1rem 0;
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
            <div class="profile-container">
                <div class="page-header">
                    <h1><i class="fas fa-users"></i> Manage Hosts</h1>
                    <p>จัดการข้อมูลเจ้าของบ้านพักทั้งหมด</p>
                </div>
                <div class="auth-tabs">
                    <div class="tab active" id="host-active-tab">
                        <i class="fas fa-sign-in-alt"></i>บัญชีที่อนุมัติ
                    </div>
                    <div class="tab" id="host-pending-tab">
                        <i class="fa-solid fa-users-line"></i> บัญชีที่รออนุมัติ
                    </div>
                    <div class="tab" id="host-cancelled-tab">
                        <i class="fa-solid fa-users-slash"></i> บัญชีที่ไม่อนุมัติ
                    </div>
                    <div class="tab" id="host-banned-tab">
                        <i class="fa-solid fa-ban"></i> บัญชีที่แบน
                    </div>
                </div>
                <div class="table-responsive active" id="host-active-section">
                    <?php if (count($hosts) > 0): ?>
                    <?php echo "<h1 class='UserH2'>รายชื่อทั้งหมด (" . count($hosts) . ")</h1>" ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ลำดับที่</th>
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
                                <td><?php echo  htmlspecialchars($host['Host_phone']); ?>
                                </td>
                                <td>
                                    <?php
                                            $status = ($host['Host_Status'] == 'active') ? "ใช้งานได้" : "ถูกระงับ";
                                            $statusClass = ($host['Host_Status'] == 'active') ? 'status-active' : 'status-inactive';
                                            ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-edit" title="แก้ไข"
                                        data-host-id="<?php echo htmlspecialchars($host['Host_id']); ?>"
                                        data-firstname="<?php echo htmlspecialchars($host['Host_firstname']); ?>"
                                        data-lastname="<?php echo htmlspecialchars($host['Host_lastname']); ?>"
                                        data-email="<?php echo htmlspecialchars($host['Host_email']); ?>"
                                        data-phone="<?php echo htmlspecialchars($host['Host_phone']); ?>"
                                        onclick="openEditHostModal(this)"><i class="fas fa-edit"
                                            name="edit_host"></i></button>
                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;"
                                        onsubmit="return confirm('คุณต้องการแบนเจ้าของบ้านพักนี้หรือไม่?');">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                        <button type="submit" class="btn btn-delete" name="ban_host" title="ลบ"><i
                                                class="fas fa-ban"></i></button>
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
                <div class="table-responsive " id="host-banned-section" style="display:none;">
                    <?php if (count($ban_host) > 0): ?>
                    <?php echo "<h1 class='UserH2'>รายชื่อทั้งหมด (" . count($ban_host) . ")</h1>" ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ลำดับที่</th>
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
                                <td><?php echo  htmlspecialchars($host['Host_phone']); ?>
                                </td>
                                <td>
                                    <?php
                                            $status = ($host['Host_Status'] == 'banned') ? "แบน" : "ไม่ผ่านการอนุมัติ";
                                            $statusClass = ($host['Host_Status'] == 'banned') ? 'status-inactive' : 'status-active';
                                            ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                        <button type="submit" class="btn btn-approve" title="ปลดแบน" name="rej_host"><i
                                                class="fa-solid fa-repeat"></i></i></button>

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
                <div class="table-responsive " id="host-pending-section" style="display:none;">
                    <?php if (count($verify_host) > 0): ?>
                    <?php echo "<h1 class='UserH2'>รายชื่อทั้งหมด (" . count($verify_host) . ")</h1>" ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ลำดับที่</th>
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
                                <td><?php echo  htmlspecialchars($host['Host_phone']); ?>
                                </td>
                                <td>
                                    <?php
                                            $status = ($host['Host_Status'] == 'pending_verify') ? "รอการตรวจสอบ" : "ไม่อนุมัติ";
                                            $statusClass = ($host['Host_Status'] == 'pending_verify') ? 'status-pending' : 'status-inactive';
                                            ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;"
                                        onsubmit="return confirm('คุณไม่อนุมัติบัญชีเจ้าของบ้านพักนี้หรือไม่?');">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                        <button type="submit" class="btn btn-delete" title="ไม่อนุมัติ"
                                            name="cancel_host"><i class="fa-solid fa-thumbs-down"></i></button>
                                    </form>
                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">

                                        <button type="submit" class="btn btn-approve" title="อนุมัติ"
                                            name="approve_host"><i class="fa-regular fa-thumbs-up"></i></button>

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
                <div class="table-responsive " id="host-cancelled-section" style="display:none;">
                    <?php if (count($cancel_host) > 0): ?>
                    <?php echo "<h1 class='UserH2'>รายชื่อทั้งหมด (" . count($ban_host) . ")</h1>" ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ลำดับที่</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th>อีเมล</th>
                                <th>เบอร์โทร</th>
                                <th>สถานะ</th>
                                <th>จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cancel_host as $host): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($host['Host_id']); ?></td>
                                <td><?php echo htmlspecialchars($host['Host_firstname'] . ' ' . $host['Host_lastname']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($host['Host_email']); ?></td>
                                <td><?php echo  htmlspecialchars($host['Host_phone']); ?>
                                </td>
                                <td>
                                    <?php
                                            $status = ($host['Host_Status'] == 'banned') ? "แบน" : "ไม่ผ่านการอนุมัติ";
                                            $statusClass = ($host['Host_Status'] == 'banned') ? 'status-inactive' : 'status-active';
                                            ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>
                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;">
                                        <input type="hidden" name="host_id" value="<?php echo $host['Host_id']; ?>">
                                        <button type="submit" class="btn btn-approve" title="ตรวจสอบใหม่"
                                            name="rej_host"><i class="fa-solid fa-repeat"></i></i></button>

                                    </form>
                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;"
                                        onsubmit="return confirm('คุณต้องการลบผู้ใช้นี้หรือไม่?');">
                                        <input type="hidden" name="host_id" value="<?php echo $user['Host_id']; ?>">
                                        <button type="submit" class="btn btn-delete" title="ลบ" id="del-btn"
                                            name="del_host"><i class="fas fa-trash"></i></button>
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
    <!-- <div id="EditHostsModal" class="modal" style="display:none;">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('EditHostsModal').style.display='none'">&times;</span>
            <h2 class="modal-header">แก้ไขเจ้าของบ้าน</h2>
            <form method="post" action="../../controls/manage_toggle.php">
                <input type="hidden" name="host_id" id="edit_host_id">
                <input type="text" class="modal-input" name="Firstname"
                    placeholder="<?php /*echo htmlspecialchars($host['Host_firstname'])*/ ?>">
                <input type="text" class="modal-input" name="Lastname"
                    placeholder="<?php /*echo htmlspecialchars($host['Host_lastname'])/* ?>">
                <input type="text" class="modal-input" name="Email"
                    placeholder="<?php /*echo htmlspecialchars($host['Host_email'])*/ ?>">
                <input type="text" class="modal-input" name="Phone"
                    placeholder="<?php /*echo htmlspecialchars($host['Host_phone'])*/ ?>">
                <button type="submit" class="btn btn-save" name="edit_host">บันทึก</button>
            </form>
        </div>
    </div> -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-with-sidebar');
            sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("sidebar-collapsed");
        }

        // function openEditHostModal(btnElement) {
        //     const hostId = btnElement.dataset.hostId;
        //     const firstname = btnElement.dataset.firstname;
        //     const lastname = btnElement.dataset.lastname;
        //     const email = btnElement.dataset.email;
        //     const phone = btnElement.dataset.phone;
        //     const modal = document.querySelector('.modal');
        //     modal.querySelector("#edit_host_id").value = hostId;
        //     modal.querySelector("input[name='Firstname']").value = firstname;
        //     modal.querySelector("input[name='Lastname']").value = lastname;
        //     modal.querySelector("input[name='Email']").value = email;
        //     modal.querySelector("input[name='Phone']").value = phone;
        //     modal.style.display = "flex";

        // }

        // function closeEditHostsModal() {
        //     document.getElementById('EditHostsModal').style.display = 'none';
        // }
        // window.onclick = function(event) {
        //     const editmodals = document.getElementById('EditHostsModal');
        //     if (event.target === editmodals) {
        //         closeEditHostsModal();
        //     }
        // }
        const tabs = [
            document.getElementById('host-active-tab'),
            document.getElementById('host-banned-tab'),
            document.getElementById('host-pending-tab'),
            document.getElementById('host-cancelled-tab')
        ];
        const sections = [
            document.getElementById('host-active-section'),
            document.getElementById('host-banned-section'),
            document.getElementById('host-pending-section'),
            document.getElementById('host-cancelled-section')
        ];

        // 2. สร้างฟังก์ชันเดียวเพื่อจัดการการคลิก
        function showTab(tabToShow, sectionToShow) {
            // ซ่อนทุกอย่างก่อน
            tabs.forEach(tab => tab.classList.remove('active'));
            sections.forEach(section => section.style.display = 'none');
            // แสดงเฉพาะอันที่เลือก
            tabToShow.classList.add('active');
            sectionToShow.style.display = 'block';
        }

        // 3. วนลูปเพื่อผูก Event ให้กับทุก Tab
        tabs.forEach((tab, index) => {
            // ตรวจสอบว่า element นั้นมีอยู่จริงก่อนที่จะเพิ่ม event
            if (tab) {
                tab.addEventListener('click', function() {
                    showTab(tab, sections[index]);
                });
            }
        });
        const hostActiveTab = document.getElementById('host-active-tab');
        const hostBannedTab = document.getElementById('host-banned-tab');
        const hostPendingTab = document.getElementById('host-pending-tab');
        const hostCancelledTab = document.getElementById('host-cancelled-tab');
        const hostActiveSection = document.getElementById('host-active-section');
        const hostBannedSection = document.getElementById('host-banned-section');
        const hostPendingSection = document.getElementById('host-pending-section');
        const hostCancelledSection = document.getElementById('host-cancelled-section');
        hostActiveTab.addEventListener('click', function() {
            hostActiveTab.classList.add('active');
            hostBannedTab.classList.remove('active');
            hostPendingTab.classList.remove('active');
            hostCancelledTab.classList.remove('active');
            hostActiveSection.style.display = 'block';
            hostBannedSection.style.display = 'none';
            hostPendingSection.style.display = 'none';
            hostCancelledSection.style.display = 'none';
        });
        hostBannedTab.addEventListener('click', function() {
            hostActiveTab.classList.remove('active');
            hostBannedTab.classList.add('active');
            hostPendingTab.classList.remove('active');
            hostCancelledTab.classList.remove('active');
            hostActiveSection.style.display = 'none';
            hostBannedSection.style.display = 'block';
            hostPendingSection.style.display = 'none';
            hostCancelledSection.style.display = 'none';
        });
        hostPendingTab.addEventListener('click', function() {
            hostPendingTab.classList.add('active');
            hostActiveTab.classList.remove('active');
            hostBannedTab.classList.remove('active');
            hostCancelledTab.classList.remove('active');
            hostActiveSection.style.display = 'none';
            hostPendingSection.style.display = 'block';
            hostBannedSection.style.display = 'none';
            hostCancelledSection.style.display = 'none';

        });
        hostCancelledTab.addEventListener('click', function() {
            hostPendingTab.classList.remove('active');
            hostActiveTab.classList.remove('active');
            hostBannedTab.classList.remove('active');
            hostCancelledTab.classList.add('active');
            hostActiveSection.style.display = 'none';
            hostPendingSection.style.display = 'none';
            hostBannedSection.style.display = 'none';
            hostCancelledSection.style.display = 'block';

        });
    });
    </script>
</body>

</html>