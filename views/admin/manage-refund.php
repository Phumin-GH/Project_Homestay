<?php
session_start();
// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}

// *** 중요 ***
// ในส่วนนี้ คุณต้องสร้างไฟล์ API เพื่อดึงข้อมูลการขอคืนเงินทั้งหมด
require_once __DIR__ . '/../../api/get_refund.php';
// สมมติว่า $refunds เป็น array ที่ได้จาก API ด้านบน
// ด้านล่างนี้คือข้อมูลตัวอย่างเพื่อให้หน้าเว็บแสดงผลได้ก่อน


?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ตรวจสอบคำขอคืนเงิน - Admin Dashboard</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .admin-container {
        max-width: 1300px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
        color: white;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        text-align: center;
    }

    .refund-section {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 2rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .refund-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }

    .refund-table th,
    .refund-table td {
        padding: 1rem 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e5e5e5;
    }

    .refund-table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .refund-table tr:hover {
        background-color: #f1f3f5;
    }

    .status-pending {
        color: #fd7e14;
        font-weight: bold;
    }

    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .btn-approve {
        background: #10b981;
        color: white;
    }

    .btn-approve:hover {
        background: #059669;
    }

    .btn-reject {
        background: #ef4444;
        color: white;
    }

    .btn-reject:hover {
        background: #dc2626;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #666;
    }

    .refund-tabs {
        display: flex;
        background: #f8f9fa;
        border-bottom: 1px solid #e5e5e5;
        margin: 1.5rem 0 0 0;

    }

    .tab {
        flex: 1;
        padding: 1rem;
        text-align: center;
        cursor: pointer;
        font-weight: 500;
        color: #666666;
        background: #ddf1faff;
        border-radius: 20px 30px 0 0;
        transition: all 0.2s ease;
        position: relative;
        filter: blur(1px);
        opacity: 0.9;
        z-index: 500;
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
        background: #cdecf9ff;
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
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h1><img src="../../public/images/logo.png" alt="Logo" class="logo-image"
                        style="width: 3.5rem; height: 3.5rem;"> Homestay bookings</h1>
            </div>
        </nav>
    </header>
    <div class="layout">
        <aside class="sidebar" id="sidebar">
            <button class="toggle-sidebar" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            <ul class="sidebar-menu">
                <li><a href="admin-dashboard.php" title="หน้าแดชบอร์ด"><i class=" fa-solid fa-ranking-star"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a></li>
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก"><i
                            class="fa-solid fa-house-medical-circle-check"></i><span class="menu-label">Approve
                            Properties</span></a></li>
                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                            class="menu-label">Hosts</span></a></li>
                <li><a href="manage-users.php" title="จัดการผู้ใช้งาน"><i class="fas fa-user-friends"></i><span
                            class="menu-label">Users</span></a></li>
                <li><a href="manage-refund.php" title="คำร้องขอคืนเงินผู้ใช้งาน" class="active"><i
                            class="fas fa-hand-holding-usd"></i><span class="menu-label">Refund</span></a></li>
                <li><a href="manage-reviews.php" title="รีวิวจากผู้ใช้งาน"><i class="fas fa-star"></i><span
                            class="menu-label">Reviews</span></a></li>
                <li><a href="violations.php" title="จัดการเรื่องร้องเรียน"><i
                            class="fas fa-exclamation-triangle"></i><span class="menu-label">Violations</span></a></li>
                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul>
        </aside>
        <?php

        if (isset($_SESSION['msg'])) {
            // echo "<div class='alert alert-success'><i class='fa-solid fa-check'></i>" . $_SESSION['msg'] . "</div>";
            echo "<script> alert(" . json_encode($_SESSION['msg']) . "); </script>";
            unset($_SESSION['msg']);
        } ?>
        <div class="main-with-sidebar">
            <div class="admin-container">
                <div class="page-header">
                    <h1><i class="fas fa-hand-holding-usd"></i> ตรวจสอบคำขอคืนเงิน</h1>
                    <p>จัดการและดำเนินการตามคำขอคืนเงินจากผู้ใช้งาน</p>
                </div>
                <div class="refund-tabs">
                    <div class="tab active" id="refund-tab">
                        <i class="fas fa-sign-in-alt"></i> บัญชีที่ยื่นขอคืนเงิน
                    </div>
                    <div class="tab" id="complete-tab">
                        <i class="fas fa-user-plus"></i> บัญชีที่ได้รับการอนุมัติ
                    </div>
                </div>
                <div class="refund-section active" id="refund-section">
                    <h2 class="section-title">รายการที่รอการตรวจสอบ (<?php echo count($refunds); ?>)</h2>
                    <?php if (count($refunds) > 0): ?>
                    <div style="overflow-x:auto;">
                        <table class="refund-table">
                            <thead>
                                <tr>
                                    <th>Refund ID</th>
                                    <th>Charge ID</th>
                                    <th>ชื่อที่พัก</th>
                                    <th>ผู้ขอคืนเงิน</th>
                                    <th>เจ้าของที่พัก</th>
                                    <th>จำนวนเงิน (บาท)</th>
                                    <th>เหตุผล</th>
                                    <th>วันที่ส่งคำขอ</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($refunds as $refund): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($refund['Refund_id']); ?></td>
                                    <td><?php echo htmlspecialchars($refund['Charge_id']); ?></td>
                                    <td><?php echo htmlspecialchars($refund['Property_name']); ?></td>
                                    <td><?php echo htmlspecialchars($refund['Firstname'] . '' . $refund['Lastname']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($refund['Host_firstname'] . '' . $refund['Host_lastname']); ?>
                                    </td>
                                    <td><?php echo number_format($refund['Refund_amount'], 2); ?></td>
                                    <td style="max-width: 250px;">
                                        <?php echo htmlspecialchars($refund['Refund_reason']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($refund['Refund_date']); ?></td>
                                    <td><span
                                            class="status-pending"><?php echo ucfirst($refund['Refund_status']); ?></span>
                                    </td>
                                    <td>
                                        <form action="../../controls/refund_actions.php" method="POST"
                                            style="display:inline;"
                                            onsubmit="return confirm('คุณต้องอนุมัติรายการคืนเงินนี้ใช่หรือไม่?');">
                                            <input type="hidden" name="booking_id"
                                                value="<?php echo $refund['Booking_id']; ?>">
                                            <button type="submit" name="admin_approve" value="approve"
                                                class="btn btn-approve">อนุมัติ</button>
                                        </form>
                                        <form action="../../controls/refund_actions.php" method="POST"
                                            style="display:inline;">
                                            <input type="hidden" name="booking_id"
                                                value="<?php echo $refund['Booking_id']; ?>">
                                            <button type="submit" name="admin_reject" value="reject"
                                                class="btn btn-reject">ปฏิเสธ</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>ไม่พบคำขอคืนเงินที่รอการอนุมัติ</h3>
                        <p>ไม่มีรายการที่ต้องดำเนินการในขณะนี้</p>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="refund-section" id="refund-section-processed" style="display: none; ">
                    <h2 class="section-title">รายการที่รอการตรวจสอบ (<?php echo count($refunds); ?>)</h2>
                    <?php if (count($refund_com) > 0): ?>
                    <div style="overflow-x:auto;">
                        <table class="refund-table">
                            <thead>
                                <tr>
                                    <th>Booking ID</th>
                                    <th>ชื่อที่พัก</th>
                                    <th>ผู้ขอคืนเงิน</th>
                                    <th>เจ้าของที่พัก</th>
                                    <th>จำนวนเงิน (บาท)</th>
                                    <th>เหตุผล</th>
                                    <th>วันที่ส่งคำขอ</th>
                                    <th>สถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($refund_com as $refund): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($refund['Booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($refund['Property_name']); ?></td>
                                    <td><?php echo htmlspecialchars($refund['Firstname'] . '' . $refund['Lastname']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($refund['Host_firstname'] . '' . $refund['Host_lastname']); ?>
                                    </td>
                                    <td><?php echo number_format($refund['Refund_amount'], 2); ?></td>
                                    <td style="max-width: 250px;">
                                        <?php echo htmlspecialchars($refund['Refund_reason']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($refund['Refund_date']); ?></td>
                                    <td><span
                                            class="status-pending"><?php echo ucfirst($refund['Refund_status']); ?></span>
                                    </td>
                                    <td>
                                        <!-- <form action="../../controls/refund_actions.php" method="POST"
                                            style="display:inline;">
                                            <input type="hidden" name="booking_id"
                                                value="<?php /*echo $refund['Booking_id'];*/ ?>">
                                            <button type="submit" name="approve" value="approve"
                                                class="btn btn-approve">อนุมัติ</button>
                                        </form>
                                        <form action="../../controls/refund_actions.php" method="POST"
                                            style="display:inline;">
                                            <input type="hidden" name="booking_id"
                                                value="<?php /*echo $refund['Booking_id'];*/ ?>">
                                            <button type="submit" name="cancel" value="reject"
                                                class="btn btn-reject">ปฏิเสธ</button>
                                        </form> -->
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>ไม่พบคำขอคืนเงินที่รอการอนุมัติ</h3>
                        <p>ไม่มีรายการที่ต้องดำเนินการในขณะนี้</p>
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

    const refundTab = document.getElementById('refund-tab');
    const completeTab = document.getElementById('complete-tab');
    const refundSection = document.getElementById('refund-section');
    const refundSectionProcessed = document.getElementById('refund-section-processed');
    refundTab.addEventListener('click', () => {
        refundTab.classList.add('active');
        completeTab.classList.remove('active');
        refundSection.style.display = 'block';
        refundSectionProcessed.style.display = 'none';
    });
    completeTab.addEventListener('click', () => {
        completeTab.classList.add('active');
        refundTab.classList.remove('active');
        refundSection.style.display = 'none';
        refundSectionProcessed.style.display = 'block';
    });
    </script>
</body>

</html>