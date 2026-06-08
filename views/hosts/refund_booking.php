<?php
session_start();
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}
require_once  __DIR__ . "/../../api/get_refund.php";
require_once  __DIR__ . "/../../controls/log_hosts.php";
// For a real scenario, this data would come from your database

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อนุมัติคำขอเงิน - Host Dashboard</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    /* Global Styles */
    body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        background-color: #f4f7f6;
        margin: 0;
        color: #333;
    }

    /* Layout */
    .page-container {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        flex-grow: 1;
        padding: 2rem;
    }

    /* Page Header Style */
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

    /* Minimal Table Style */
    .data-table {
        width: 100%;
        position: relative;
        border-collapse: collapse;
        background-color: #ffffff;
        border-radius: 0 0 8px 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        z-index: 900;
    }

    .data-table th,
    .data-table td {
        padding: 1rem;
        text-align: left;
    }

    .data-table td {
        font-size: 0.8rem;
        font-weight: 500;
    }

    .data-table thead {
        background-color: #1e5470;
    }

    .data-table th {
        font-weight: 600;
        color: white;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table tbody tr {
        border-bottom: 1px solid #f1f1f1;
    }

    .data-table tbody tr:last-child {
        border-bottom: none;
    }

    .data-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* Status & Action Styles */
    .status {
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-pending {
        background-color: #fff0c2;
        color: #856404;
    }

    .status-approved {
        background-color: #d4edda;
        color: #155724;
    }

    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
    }

    .action-btn {
        background: none;
        border: 1px solid #ccc;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s ease;

    }

    .btn-approve {
        border-color: #28a745;
        color: #28a745;
    }

    .btn-approve:hover {
        background-color: #28a745;
        color: white;
    }

    .btn-reject {
        border-color: #dc3545;
        color: #dc3545;
    }

    .btn-reject:hover {
        background-color: #dc3545;
        color: white;
    }

    .btn-disabled {
        background-color: #e9ecef;
        color: #6c757d;
        border: solid 2px #333;
        border-radius: 8px;
        padding: 0.4rem 0.8rem;
        cursor: not-allowed;
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

    /* --- Empty State --- */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #ffffff;
        border-radius: 0 0 8px 8px;
        /* border: 1px dashed #dee2e6; */
        border: 1px solid #dee2e6;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #999;
        margin-bottom: 2rem;
        max-width: 400px;
        margin-left: auto;
        margin-right: auto;
    }

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
        background-color: #1e5470;
        color: white;
        width: 17rem;
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
                <?php if ($hosts['Host_Status'] == 'pending_verify'): ?>
                <li><a href="add-property.php" title="ลงทะเบียนบ้านพักใหม่"><i class="fas fa-user-plus"></i>
                        <span class="menu-label">ลงทะเบียนบ้านพักใหม่</span></a></li>
                <?php endif; ?>
                <li><a href="host-dashboard.php" title="รายงานข้อมูล" class="active"><i
                            class="fa-solid fa-ranking-star"></i><span class="menu-label">รายงานข้อมูล</span></a></li>
                <li><a href="profile.php" title="ข้อมูลส่วนตัว"><i class="fas fa-user"></i><span
                            class="menu-label">ข้อมูลส่วนตัว</span></a>
                </li>
                <?php if ($hosts['Host_Status'] == 'active'): ?>
                <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                            class="menu-label">จัดการบ้านพัก</span></a></li>
                <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                            class="menu-label">รายการการจอง</span></a></li>
                <li><a href="refund_booking.php" title="การขอคืนเงิน" class="active"><i
                            class="fa-solid fa-money-bill-transfer"></i><span class="menu-label">การขอคืนเงิน</span></a>
                </li>
                <li><a href="walkin-property.php" title="รายการจองบ้านพัก"><i
                            class="fa-solid fa-person-walking"></i><span class="menu-label">รายการจองบ้านพัก</span></a>
                </li>
                <?php endif; ?>
                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">ออกจากระบบ</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <div class="page-container">
                <main class="main-content">

                    <div class="page-header">
                        <h1><i class="fas fa-hand-holding-usd"></i> รายการขอคืนเงิน</h1>
                        <p>รายการขอคืนเงิน</p>
                    </div>
                    <div class="refund-tabs">
                        <div class="tab active" id="refund-tab">
                            <i class="fas fa-sign-in-alt"></i> บัญชีที่ยื่นขอคืนเงิน
                        </div>
                        <div class="tab" id="complete-tab">
                            <i class="fas fa-user-plus"></i> บัญชีที่ได้รับการอนุมัติ
                        </div>
                        <div class="tab" id="cancel-tab">
                            <i class="fas fa-user-plus"></i> บัญชีที่ไม่อนุมัติ
                        </div>

                    </div>
                    <div class="table-container active" id="table-refund-container">
                        <?php if (count($refund) > 0): ?>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>บ้านพัก</th>
                                    <th>ผู้ใช้</th>
                                    <th>วันที่</th>
                                    <th>จำนวนเงิน</th>
                                    <th>เหตุผล</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($refund as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['Booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($request['Property_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['Firstname'] . '' . $request['Lastname']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['Refund_date']); ?></td>
                                    <td><?php echo number_format($request['Refund_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($request['Refund_reason']); ?></td>
                                    <td>
                                        <?php
                                                $status_class = 'status-pending';
                                                if ($request['Host_Check'] === 'successful') $status_class = 'status-approved';
                                                if ($request['Host_Check'] === 'failed') $status_class = 'status-rejected';
                                                ?>
                                        <span class="status <?php echo $status_class; ?>">
                                            <?php echo isset($request['Host_Check']) ? 'รออนุมัติ' : 'ไม่พบข้อมูล'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="button-actions">
                                            <!-- <button class="action-btn btn-approve"
                                                onclick="handleAction(this,'approve')">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                            <button class="action-btn btn-reject"
                                                onclick="handleAction(this, 'cancel')">
                                                <i class="fa-solid fa-times"></i>
                                            </button> -->
                                            <form method="post" action="../../controls/refund_actions.php"
                                                style="display:inline;"
                                                onsubmit="return confirm('คุณต้องอนุมัติรายการคืนเงินนี้ใช่หรือไม่?');">
                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $request['Booking_id']; ?>">
                                                <button type="submit" title="อนุมัติ" name="host_approve"
                                                    class="action-btn btn-approve">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="post" action="../../controls/refund_actions.php"
                                                style="display:inline;"
                                                onsubmit="return confirm('คุณต้องการแบนเจ้าของบ้านพักนี้หรือไม่?');">
                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $request['Booking_id']; ?>">
                                                <button type="submit" title="ไม่อนุมัติ" name="host_reject"
                                                    class="action-btn btn-reject">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            </form>


                                        </div>


                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="ph ph-heart-break"></i>
                            <h3>ไม่พบรายการขอคืนเงิน</h3>
                            <p>ยังไม่มีคำขอคืนเงินในขณะนี้ กรุณาตรวจสอบอีกครั้งภายหลัง</p>
                            <a href="host-dashboard.php" class="btn btn-primary">รายงานข้อมูล</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="table-container" id="table-complete-container" style="display: none;">
                        <?php if (count($refund_complete) > 0): ?>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>บ้านพัก</th>
                                    <th>ผู้ใช้</th>
                                    <th>วันที่</th>
                                    <th>จำนวนเงิน</th>
                                    <th>เหตุผล</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($refund_complete as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['Booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($request['Property_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['Firstname'] . '' . $request['Lastname']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['Refund_date']); ?></td>
                                    <td><?php echo number_format($request['Refund_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($request['Refund_reason']); ?></td>
                                    <td>
                                        <?php
                                                $status_class = 'status-pending';
                                                if ($request['Host_Check'] === 'approve') $status_class = 'status-approved';
                                                if ($request['Host_Check'] === 'unapprove') $status_class = 'status-rejected';
                                                ?>
                                        <span class="status <?php echo $status_class; ?>">
                                            <?php echo isset($request['Host_Check']) ? 'อนุมัติ' : 'ไม่พบข้อมูล'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-disabled" disabled title="disable"><i
                                                class="fa-solid fa-ban"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="ph ph-heart-break"></i>
                            <h3>ไม่พบรายการขอคืนเงิน</h3>
                            <p>ยังไม่มีคำขอคืนเงินในขณะนี้ กรุณาตรวจสอบอีกครั้งภายหลัง</p>
                            <a href="host-dashboard.php" class="btn btn-primary">รายงานข้อมูล</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="table-container" id="table-cancel-container" style="display: none;">
                        <?php if (count($refund_failed) > 0): ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>บ้านพัก</th>
                                    <th>ผู้ใช้</th>
                                    <th>วันที่</th>
                                    <th>จำนวนเงิน</th>
                                    <th>เหตุผล</th>
                                    <th>สถานะ</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($refund_failed as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['Booking_id']); ?></td>
                                    <td><?php echo htmlspecialchars($request['Property_name']); ?></td>
                                    <td><?php echo htmlspecialchars($request['Firstname'] . '' . $request['Lastname']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($request['Refund_date']); ?></td>
                                    <td><?php echo number_format($request['Refund_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($request['Refund_reason']); ?></td>
                                    <td>
                                        <?php
                                                $status_class = 'status-pending';
                                                if ($request['Host_Check'] === 'approve') $status_class = 'status-approved';
                                                if ($request['Host_Check'] === 'unapprove') $status_class = 'status-rejected';
                                                ?>
                                        <span class="status <?php echo $status_class; ?>">
                                            <?php echo isset($request['Host_Check']) ? 'อนุมัติ' : 'ไม่พบข้อมูล'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-disabled" disabled title="disable"><i
                                                class="fa-solid fa-ban"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <div class="empty-state">
                            <i class="ph ph-heart-break"></i>
                            <h3>ไม่พบรายการขอคืนเงิน</h3>
                            <p>ยังไม่มีคำขอคืนเงินในขณะนี้ กรุณาตรวจสอบอีกครั้งภายหลัง</p>
                            <a href="host-dashboard.php" class="btn btn-primary">รายงานข้อมูล</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </main>
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
    const refund_table = document.getElementById('table-refund-container');
    const complete_table = document.getElementById('table-complete-container');
    const cancel_table = document.getElementById('table-cancel-container');
    const refund_tab = document.getElementById('refund-tab');
    const complete_tab = document.getElementById('complete-tab');
    const cancel_tab = document.getElementById('cancel-tab');

    refund_tab.addEventListener('click', function() {
        refund_tab.classList.add('active');
        complete_tab.classList.remove('active');
        cancel_tab.classList.remove('active');
        refund_table.style.display = 'block';
        complete_table.style.display = 'none';
        cancel_table.style.display = 'none';


    });
    complete_tab.addEventListener('click', function() {
        refund_tab.classList.remove('active');
        complete_tab.classList.add('active');
        cancel_tab.classList.remove('active');
        refund_table.style.display = 'none';
        complete_table.style.display = 'block';
        cancel_table.style.display = 'none';


    });
    cancel_tab.addEventListener('click', function() {
        refund_tab.classList.remove('active');
        complete_tab.classList.remove('active');
        cancel_tab.classList.add('active');
        refund_table.style.display = 'none';
        complete_table.style.display = 'none';
        cancel_table.style.display = 'block';
    });
    // const tabs = [
    //     document.getElementById('refund-tab'),
    //     document.getElementById('complete-tab'),
    //     document.getElementById('cancel-tab')
    // ];
    // const containers = [
    //     ocument.getElementById('table-refund-container'),
    //     document.getElementById('table-complete-container'),
    //     document.getElementById('table-cancel-container')

    // ];

    // function showTab(tabToShow, containerToShow) {
    //     tabs.forEach(tab => tab.classList.remove('active'));
    //     containers.forEach(container => container.style.display = 'none');
    //     tabToShow.classList.add('active');
    //     containerToShow.style.display = 'block';
    // }

    // tabs.forEach((tab, index) => {
    //     if (tab) {
    //         tab.addEventListener('click', function() {
    //             showTab(tab, containers[index]);
    //         });
    //     }
    // });
    </script>
</body>

</html>