<?php
session_start();
// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}
require_once __DIR__ . '/../../api/get_violation_reports.php';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการเรื่องร้องเรียน - Admin Dashboard</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .admin-container {
        max-width: 1400px;
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

    .table-container {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .table th,
    .table td {
        padding: 1.25rem 1.5rem;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        color: #495057;
    }

    .table tbody tr:hover {
        background-color: #f1f3f5;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        gap: 5px;
    }

    .btn-view {
        background-color: #007bff;
        color: white;
    }

    .btn-view:hover {
        background-color: #0056b3;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #b02a37;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
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
                <li><a href="admin-dashboard.php" title="รายงานข้อมูล"><i class="fa-solid fa-ranking-star"></i><span
                            class="menu-label">รายงานข้อมูล</span></a></li>
                <li><a href="profile.php" title="ข้อมูลส่วนตัว"><i class="fas fa-user"></i><span
                            class="menu-label">ข้อมูลส่วนตัว</span></a></li>
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก"><i
                            class="fa-solid fa-house-medical-circle-check"></i><span
                            class="menu-label">อนุมัติสถานที่พัก</span></a></li>
                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                            class="menu-label">ข้อมูลเจ้าของบ้านที่พัก</span></a></li>
                <li><a href="manage-users.php" title="จัดการผู้ใช้งาน"><i class="fas fa-user-friends"></i><span
                            class="menu-label">ข้อมูลผู้ใช้</span></a></li>
                <li><a href="manage-refund.php" title="คำร้องขอคืนเงินผู้ใช้งาน"><i
                            class="fas fa-hand-holding-usd"></i><span
                            class="menu-label">จัดการคำร้องขอคืนเงิน</span></a></li>
                <li><a href="manage-reviews.php" title="รีวิวจากผู้ใช้"><i class="fas fa-star"></i><span
                            class="menu-label">รีวิวจากผู้ใช้งาน</span></a></li>
                <li><a href="violations.php" title="จัดการเรื่องร้องเรียน" class="active"><i
                            class="fas fa-exclamation-triangle"></i><span
                            class="menu-label">จัดการเรื่องร้องเรียน</span></a></li>
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
                    <h1><i class="fas fa-exclamation-triangle"></i> จัดการเรื่องร้องเรียน</h1>
                    <p>เรื่องที่ร้องเรียนเข้ามา</p>
                </div>

                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>รหัสรีวิว</th>
                                    <th>ผู้รายงาน</th>
                                    <th>ผู้ถูกรายงาน</th>
                                    <th>เหตุผล</th>
                                    <th>วันที่</th>
                                    <th>ดูรีวิว</th>
                                    <th>จัดการรายงานรีวิว</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($reports)) : ?>
                                <?php foreach ($reports as $report) : ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($report['Report_id']); ?></td>
                                    <td><?php echo htmlspecialchars($report['Review_id']); ?></td>
                                    <td><?php echo htmlspecialchars($report['Reported_by']); ?></td>
                                    <td><?php echo htmlspecialchars($report['Reported_user']); ?></td>
                                    <td><?php echo htmlspecialchars($report['Report_reason']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($report['Create_at'])); ?></td>
                                    <td>
                                        <form action="views_property.php" method="POST" style='display:inline;'>
                                            <input type="hidden" name="house_id"
                                                value="<?= htmlspecialchars($report['Property_id']); ?>">
                                            <input type="hidden" name="review_id"
                                                value="<?= htmlspecialchars($report['Review_id']); ?>">
                                            <button type='submit' class="btn btn-view">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                        <!-- <form action="../../controls/report_action.php" method="POST"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรีวิวนี้?');">
                                                    <input type="hidden" name="review_id"
                                                        value="<?php /*htmlspecialchars($report['Review_id']);*/ ?>">
                                                    <button type="submit" name="delete_review" class="btn btn-delete"
                                                        title="ลบรีวิว">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <form action="../../controls/report_action.php" method="POST"
                                                    style="display:inline;"
                                                    onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการซ่อนรีวิวนี้?');">
                                                    <input type="hidden" name="review_id"
                                                        value="<?php /*echo $report['Review_id'];*/ ?>">
                                                    <button type="submit" name="delete_review" class="btn btn-delete"
                                                        title="ซ่อนรีวิว">
                                                        <i class="fa-solid fa-eye-low-vision"></i>
                                                    </button>
                                                </form> -->
                                    </td>
                                    <td>
                                        <a name="nothing" class="btn btn-delete" title="ไม่พบรีวิวเชิงลบ"
                                            data-review-id="<?php echo $report['Review_id'] ?>">
                                            <i class="fa-solid fa-question"></i>
                                        </a>
                                        <a name="taken" class="btn btn-delete" title="จัดการเรียบร้อย"
                                            data-review-id="<?php echo $report['Review_id'] ?>">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else : ?>
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="fas fa-check-circle"></i>
                                            <h3>ไม่พบเรื่องร้องเรียน</h3>
                                            <p>ไม่มีรายการที่ต้องดำเนินการในขณะนี้</p>
                                        </div>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
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
    const show_btns = document.getElementsByName('taken');
    show_btns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            if (!reviewId) {
                alert('ไม่พบ review ID');
                return;
            }
            if (confirm("คุณยืนยันที่จะอัปเดตสถานะการรายงานรีวิว")) {
                btn.disabled = true;


                fetch('../../controls/report_action.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            review_id: reviewId,
                            submit_rp: true,
                            action: 'taken'
                        })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        if (data.success === true) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                            // เปิดปุ่มกลับ
                            btn.disabled = false;

                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('เกิดข้อผิดพลาด: ' + error.message);
                        // เปิดปุ่มกลับ
                        btn.disabled = false;

                    });
            }
        });
    });
    const hidden_btn = document.getElementsByName('nothing');
    hidden_btn.forEach(btn => {
        btn.addEventListener('click', function() {
            const reviewId = this.dataset.reviewId;
            if (!reviewId) {
                alert('ไม่พบ report ID');
                return;
            }
            if (confirm("คุณยืนยันที่จะอัปเดตสถานะการรายงานรีวิว")) {
                btn.disabled = true;
                fetch('../../controls/report_action.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            review_id: reportId,
                            submit_rp: true,
                            action: 'nothing'
                        })
                    })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => {
                        if (data.success === true) {
                            alert(data.message);
                            window.location.reload();
                        } else {
                            alert(data.message);
                            // เปิดปุ่มกลับ
                            btn.disabled = false;

                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('เกิดข้อผิดพลาด: ' + error.message);
                        // เปิดปุ่มกลับ
                        btn.disabled = false;

                    });
            }
        });

    });
    </script>
</body>

</html>