<?php
session_start();
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}
include __DIR__ . '/../config/db_connect.php';

if (isset($_SESSION["Admin_email"])) {
    $stmt = $conn->prepare("SELECT * FROM Property INNER JOIN Host on Property.Host_id = Host.Host_id WHERE Property.Property_status = 0");
    $stmt->execute();
    $homestay = $stmt->fetchAll(PDO::FETCH_ASSOC);
} 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อนุมัติบ้านพัก - Admin Dashboard</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
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

    .properties-section {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e5e5;
        padding: 2rem;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .property-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e5e5e5;
        transition: all 0.3s ease;
    }

    .property-card:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .property-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .property-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .property-location {
        color: #666;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .property-image {
        width: 120px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin-left: 1rem;
    }

    .host-info {
        background: #e3f2fd;
        border-radius: 8px;
        padding: 1rem;
        margin: 1rem 0;
    }

    .host-info h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #1565c0;
        margin-bottom: 0.5rem;
    }

    .host-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #666;

    }

    .property-actions {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-approve {
        background: #10b981;
        color: white;
    }

    .btn-approve:hover {
        background: #059669;
        transform: translateY(-1px);
    }

    .btn-reject {
        background: #ef4444;
        color: white;
    }

    .btn-reject:hover {
        background: #dc2626;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #666;
    }

    .empty-state i {
        font-size: 4rem;
        color: #1e5470;
        margin-bottom: 1.5rem;
    }

    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .empty-state p {
        color: #999;
    }

    @media (max-width: 768px) {
        .admin-container {
            padding: 1rem;
        }

        .property-header {
            flex-direction: column;
        }

        .property-image {
            margin-left: 0;
            margin-top: 1rem;
            width: 10rem;
            height: 15rem;
        }

        .property-actions {
            flex-direction: column;
        }
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
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก" class="active"><i
                            class="fas fa-check-circle"></i><span class="menu-label">Approve Properties</span></a></li>
                <li><a href="manage-hosts.php" title="จัดการผู้ใช้งานสถานที่พัก"><i class="fas fa-users"></i><span
                            class="menu-label">Hosts</span></a></li>
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
                    <h1><i class="fas fa-check-circle"></i> อนุมัติบ้านพัก</h1>
                    <p>ตรวจสอบและอนุมัติบ้านพักที่รอการอนุมัติ</p>
                </div>
                <div class="properties-section">
                    <div class="section-title">
                        <i class="fas fa-clock"></i>
                        บ้านพักที่รอการอนุมัติ
                    </div>

                    <?php if (count($homestay) > 0): ?>
                    <?php echo "<h2>รายการทั้งหมด (".count( $homestay) .")</h2>" ?>
                    <?php foreach ($homestay as $property): ?>
                    <div class="property-card">
                        <div class="property-header">
                            <div>
                                <h3 class="property-title"><?php echo htmlspecialchars($property['Property_name']); ?>
                                </h3>
                                <div class="property-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    จ.<?php echo htmlspecialchars($property['Property_province']); ?>
                                    อ.<?php echo htmlspecialchars($property['Property_district']); ?>
                                    ต.<?php echo htmlspecialchars($property['Property_subdistrict']); ?>
                                </div>
                                <div><strong>พิกัด:</strong>
                                    <?php echo htmlspecialchars($property['Property_latitude']); ?>,
                                    <?php echo htmlspecialchars($property['Property_longitude']); ?></div>
                            </div>
                            <?php if (!empty($property['Property_image'])): ?>
                            <img src="../<?php echo htmlspecialchars($property['Property_image']); ?>"
                                alt="<?php echo htmlspecialchars($property['Property_name']); ?>"
                                class="property-image">
                            <?php endif; ?>
                        </div>
                        <div class="host-info">
                            <h4><i class="fas fa-user"></i> ข้อมูลเจ้าของบ้านพัก</h4>
                            <div class="host-details">
                                <div><strong>ชื่อ:</strong>
                                    <?php echo htmlspecialchars($property['Host_firstname'] . ' ' . $property['Host_lastname']); ?>
                                </div>
                                <div><strong>อีเมล:</strong> <?php echo htmlspecialchars($property['Host_email']); ?>
                                </div>
                                <div><strong>เบอร์โทร: 66+</strong>
                                    <?php echo htmlspecialchars($property['Host_phone']); ?></div>

                            </div>
                        </div>
                        <div class="property-actions">
                            <form method="POST"
                                action="../controls/approve_property.php?id=<?php echo htmlspecialchars($property['Property_id']); ?>"
                                style="display: inline;">
                                <button class="btn btn-approve" name="approve"><i class="fas fa-check"></i>
                                    อนุมัติ</button>
                            </form>
                            <form method="POST"
                                action="../controls/approve_property.php?id=<?php echo htmlspecialchars($property['Property_id']); ?>"
                                style="display: inline;">
                                <button class="btn btn-reject" name="cancel"><i class="fas fa-times"></i>
                                    ปฏิเสธ</button>
                            </form>


                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>ไม่มีบ้านพักที่รอการอนุมัติ</h3>
                        <p>บ้านพักทั้งหมดได้รับการอนุมัติแล้ว</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
    // function toggleSidebar() {
    //     const sidebar = document.getElementById('sidebar');
    //     const mainContent = document.querySelector('.main-with-sidebar');
    //     sidebar.classList.toggle("collapsed");
    //     mainContent.classList.toggle("sidebar-collapsed");
    // }

    // function approveProperty(propertyId) {
    //     if (confirm('คุณต้องการอนุมัติบ้านพักนี้หรือไม่?')) {
    //         const form = document.createElement('form');
    //         form.method = 'POST';
    //         form.innerHTML = `
    //                 <input type="hidden" name="action" value="approve">
    //                 <input type="hidden" name="property_id" value="${propertyId}">
    //             `;
    //         document.body.appendChild(form);
    //         form.submit();
    //     }
    // }

    // function rejectProperty(propertyId) {
    //     if (confirm('คุณต้องการปฏิเสธบ้านพักนี้หรือไม่?')) {
    //         const form = document.createElement('form');
    //         form.method = 'POST';
    //         form.innerHTML = `
    //                 <input type="hidden" name="action" value="reject">
    //                 <input type="hidden" name="property_id" value="${propertyId}">
    //             `;
    //         document.body.appendChild(form);
    //         form.submit();
    //     }
    // }
    </script>
</body>

</html>