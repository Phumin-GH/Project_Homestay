<?php
session_start();
// ตรวจสอบสิทธิ์ Admin
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}

// *** 중요 ***
// ในส่วนนี้ คุณต้องสร้างไฟล์ API เพื่อดึงข้อมูลการ report ทั้งหมด
// require_once __DIR__ . '/../../api/get_violation_reports.php';

// ด้านล่างนี้คือข้อมูลตัวอย่าง (Dummy Data) เพื่อให้หน้าเว็บแสดงผลได้ก่อน
$reports = [
    [
        'report_id' => 1,
        'review_id' => 101,
        'reported_by' => 'Phumin',
        'reported_user' => 'Somsak',
        'report_reason' => 'ใช้คำหยาบคายและไม่เหมาะสม',
        'report_date' => '2025-09-25 10:30:00'
    ],
    [
        'report_id' => 2,
        'review_id' => 102,
        'reported_by' => 'Jane',
        'reported_user' => 'Somchai (Host)',
        'report_reason' => 'รีวิวไม่เป็นความจริง กล่าวหาเท็จ',
        'report_date' => '2025-09-24 15:00:00'
    ]
];
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
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        background: #1e5470;
        color: white;
        padding: 3rem 2rem;
        border-radius: 16px;
        margin-bottom: 2rem;
        text-align: center;
    }

    .page-header h1 {
        margin: 0;
        font-size: 2.5rem;
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
    </style>
</head>

<body>
    <div class="main-content">
        <div class="admin-container">
            <div class="page-header">
                <h1><i class="fas fa-exclamation-triangle"></i> จัดการเรื่องร้องเรียน (Violations)</h1>
            </div>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Review ID</th>
                                <th>ผู้รายงาน</th>
                                <th>ผู้ถูกรายงาน</th>
                                <th>เหตุผล</th>
                                <th>วันที่</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($reports)) : ?>
                            <?php foreach ($reports as $report) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($report['report_id']); ?></td>
                                <td><?php echo htmlspecialchars($report['review_id']); ?></td>
                                <td><?php echo htmlspecialchars($report['reported_by']); ?></td>
                                <td><?php echo htmlspecialchars($report['reported_user']); ?></td>
                                <td><?php echo htmlspecialchars($report['report_reason']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($report['report_date'])); ?></td>
                                <td>
                                    <a href="view_review.php?review_id=<?php echo $report['review_id']; ?>"
                                        class="btn btn-view" title="ดูรีวิว">
                                        <i class="fas fa-eye"></i> ดูรีวิว
                                    </a>
                                    <form action="../../controls/violation_actions.php" method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรีวิวนี้?');">
                                        <input type="hidden" name="review_id"
                                            value="<?php echo $report['review_id']; ?>">
                                        <button type="submit" name="delete_review" class="btn btn-delete"
                                            title="ลบรีวิว">
                                            <i class="fas fa-trash"></i> ลบรีวิว
                                        </button>
                                    </form>
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
</body>

</html>