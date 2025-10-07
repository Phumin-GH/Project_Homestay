<?php
session_start();
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}
require_once __DIR__ . '/../../api/get_ListVerify.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="../../public/css/barStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .admin-container {
        max-width: 1200px;
        margin: 0 auto;
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

    .table-responsive {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 0 2rem;
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

        .form-row {
            grid-template-columns: 1fr;
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
        border-radius: 20px 30px 0 0;
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

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: #1a1a1a;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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
        max-width: 500px;
        width: 100%;
        padding: 2rem;
        position: relative;
        box-shadow: 0 2px 16px rgba(0, 0, 0, 0.15);
        margin: 10rem 0rem 0 30rem;
        overflow-y: auto;
        flex-direction: column;
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

    .close {
        position: absolute;
        right: 15px;
        top: 10px;
        font-size: 24px;
        cursor: pointer;
    }

    .closeBtn {
        background-color: #ccc;
        margin-left: 10px;
    }


    .contact-btn {
        background-color: #1e5470;
        color: white;
    }

    .contact-btn:hover {
        background-color: #2a6f97;
    }

    .action-btn {
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
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
                <li><a href="admin-dashboard.php" title="หน้าแดชบอร์ด"><i class="fa-solid fa-ranking-star"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a></li>
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก"><i
                            class="fa-solid fa-house-medical-circle-check"></i><span class="menu-label">Approve
                            Properties</span></a></li>
                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                            class="menu-label">Hosts</span></a></li>
                <li><a href="manage-users.php" title="จัดการผู้ใช้งาน" class="active"><i
                            class="fas fa-user-friends"></i><span class="menu-label">Users</span></a></li>
                <li><a href="manage-refund.php" title="คำร้องขอคืนเงินผู้ใช้งาน"><i
                            class="fas fa-hand-holding-usd"></i><span class="menu-label">Refund</span></a></li>
                <li><a href="manage-reviews.php" title="รีวิวจากผู้ใช้งาน"><i class="fas fa-star"></i><span
                            class="menu-label">Reviews</span></a></li>
                <li><a href="violations.php" title="จัดการเรื่องร้องเรียน"><i
                            class="fas fa-exclamation-triangle"></i><span class="menu-label">Violations</span></a></li>
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
                    <h1><i class="fas fa-user-friends"></i> Manage Users</h1>
                    <p>จัดการข้อมูลผู้ใช้งานระบบทั้งหมด</p>
                </div>
                <div class="auth-tabs">
                    <div class="tab active" id="user-active-tab">
                        <i class="fas fa-sign-in-alt"></i> บัญชีที่ใช้งานปกติ
                    </div>
                    <div class="tab" id="user-banned-tab">
                        <i class="fas fa-user-plus"></i> บัญชีที่ถูกแบน
                    </div>
                    <!-- <div class="tab" id="user-inactive-tab">
                        <i class="fas fa-user-plus"></i> บัญชีที่ใช้งานไม่ได้
                    </div> -->
                </div>
                <div class="table-responsive active" id="user-active-section">
                    <?php if (count($users) > 0): ?>
                    <?php echo "<h1 class='UserH2'>รายชื่อทั้งหมดบัญชีที่ใช้งานปกติ (" . count($users) . ")</h1>" ?>
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
                                <td><?php echo htmlspecialchars($user['Phone']); ?>
                                </td>
                                <td>
                                    <?php
                                            $status = ($user['User_Status'] == 'active') ? "ใช้งานปกติ" : "ใช้งานไม่ได้";
                                            $statusClass = ($user['User_Status'] == 'active') ? 'status-active' : 'status-inactive';
                                            ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>


                                    <button type="button" class="btn btn-edit" title="แก้ไข"
                                        data-user-id="<?php echo htmlspecialchars($user['User_id']); ?>"
                                        data-firstname="<?php echo htmlspecialchars($user['Firstname']); ?>"
                                        data-lastname="<?php echo htmlspecialchars($user['Lastname']); ?>"
                                        data-phone="<?php echo htmlspecialchars($user['Phone']); ?>" name="edit-btn"><i
                                            class="fas fa-edit"></i></button>
                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;"
                                        onsubmit="return confirm('คุณต้องการแบนผู้ใช้นี้หรือไม่?');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['User_id']; ?>">
                                        <button type="submit" class="btn btn-ban" title="แบน" name="ban_user"><i
                                                class="fas fa-ban"></i></button>
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

                <div class="table-responsive " id="user-banned-section" style="display:none;">

                    <?php if (count($ban_user) > 0): ?>
                    <?php echo "<h1 class='UserH2'>รายชื่อทั้งหมดบัญชีที่ถูกแบน (" . count($ban_user) . ")</h1>" ?>
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
                            <?php foreach ($ban_user as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['User_id']); ?></td>
                                <td><?php echo htmlspecialchars($user['Firstname'] . ' ' . $user['Lastname']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($user['User_email']); ?></td>
                                <td><?php echo htmlspecialchars($user['Phone']); ?>
                                </td>
                                <td>
                                    <?php
                                            $status = ($user['User_Status'] == 'banned') ? "แบน" : "ปกติ";
                                            $statusClass = ($user['User_Status'] == 'banned') ? 'status-inactive' : 'status-active';
                                            ?>
                                    <span
                                        class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                                </td>
                                <td>

                                    <form method="post" action="../../controls/manage_toggle.php"
                                        style="display:inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['User_id']; ?>">

                                        <button type="submit" class="btn btn-ban" title="แบน" id="ban-btn"
                                            name="rej_user"><i class="fas fa-ban"></i></button>


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
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="modal-header">การแก้ไขข้อมูลผู้ใช้</h2>
            <form id="editForm">
                <input type="hidden" id="userIdInput">
                <input type="text" class="modal-input" name="Firstname" id="firstname">
                <input type="text" class="modal-input" name="Lastname" id="lastname">
                <input type="number" class="modal-input" name="Phone" id="phone">
                <button type="submit" class="action-btn contact-btn">ส่งข้อมูล</button>
                <button type="button" id="closeBtn" class="btn btn-delete">ยกเลิก</button>
            </form>
        </div>
    </div>


    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById("editModal");
        const edit_btn = document.getElementsByName('edit-btn');
        const span = document.querySelector(".close");
        const closeBtn = document.getElementById("closeBtn");
        const editform = document.getElementById("editForm");
        edit_btn.forEach(btn => {
            btn.onclick = function() {
                const userId = this.dataset.userId;
                const firstname = this.dataset.firstname;
                const lastname = this.dataset.lastname;
                const phone = this.dataset.phone;
                document.getElementById("userIdInput").value = userId;
                document.getElementById('firstname').value = firstname;
                document.getElementById('lastname').value = lastname;
                document.getElementById('phone').value = phone;
                modal.style.display = "block";
            };
        });
        span.onclick = () => modal.style.display = "none";
        closeBtn.onclick = () => modal.style.display = "none";
        window.onclick = (e) => {
            if (e.target == modal) {
                modal.style.display = "none";
            }
        }
        editform.onsubmit = (e) => {
            e.preventDefault();
            const firstname = document.getElementById("firstname").value;
            const lastname = document.getElementById('lastname').value;
            const phone = document.getElementById('phone').value;
            const user_id = document.getElementById('userIdInput').value;
            if (!firstname || !lastname || !phone) {
                alert('เกิดข้อผิดพลาด: ไม่พบข้อมูลที่แก้ไข');
                return;
            }
            // ส่งข้อมูลไปยัง Server
            fetch('../../controls/edit_users.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    // ใช้ข้อมูลที่ดึงมาจาก hidden input
                    body: new URLSearchParams({
                        firstname: firstname,
                        lastname: lastname,
                        phone: phone,
                        user_id: user_id,
                        edit_user: true

                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);

                        modal.style.display = "none";
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการแก้ไขข้อมูล');
                });
        };
    });
    const tabs = [
        document.getElementById('user-active-tab'),
        document.getElementById('user-banned-tab'),
        // document.getElementById('user-inactive-tab')
    ];
    const tables = [
        document.getElementById('user-active-section'),
        document.getElementById('user-banned-section'),
        // document.getElementById('user-inactive-section')
    ];

    function showTab(tabToShow, tableToShow) {
        tabs.forEach(tab => {
            if (tab) tab.classList.remove('active')
        });
        tables.forEach(table => {
            if (tables) table.style.display = 'none'
        });
        if (tabToShow) tabToShow.classList.add('active');
        if (tabToShow) tableToShow.style.display = 'block';
        console.error('Tab Error');
    }
    tabs.forEach((tab, index) => {
        if (tab) {
            tab.addEventListener('click', function() {
                showTab(tab, tables[index]);
            });
        }
    });
    </script>
</body>

</html>