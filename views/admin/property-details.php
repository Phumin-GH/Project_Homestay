<?php
session_start();
if (!isset($_SESSION["Admin_email"])) {
    header("Location: admin-login.php");
    exit();
}

require_once __DIR__ . '/../../model/config/db_connect.php';

// Get Property ID from URL
$property_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if (!$property_id) {
    $_SESSION['error'] = "ไม่พบรหัสบ้านพัก";
    header("Location: approve-properties.php");
    exit();
}

// Fetch property details with host and rooms
$stmt = $conn->prepare("
    SELECT p.*, h.*, 
           COUNT(r.Room_id) as room_count,
           AVG(rev.Rating) as avg_rating,
           COUNT(rev.Review_id) as review_count
    FROM property p 
    INNER JOIN host h ON p.Host_id = h.Host_id 
    LEFT JOIN room r ON p.Property_id = r.Property_id
    LEFT JOIN review rev ON p.Property_id = rev.Property_id
    WHERE p.Property_id = ?
    GROUP BY p.Property_id
");
$stmt->execute([$property_id]);
$property = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$property) {
    $_SESSION['error'] = "ไม่พบข้อมูลบ้านพัก";
    header("Location: approve-properties.php");
    exit();
}

// Fetch rooms
$roomStmt = $conn->prepare("SELECT * FROM room WHERE Property_id = ? ORDER BY Room_number");
$roomStmt->execute([$property_id]);
$rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent reviews
$reviewStmt = $conn->prepare("
    SELECT r.*, u.Firstname, u.Lastname 
    FROM review r 
    INNER JOIN user u ON r.User_id = u.User_id 
    WHERE r.Property_id = ? 
    ORDER BY r.Review_date DESC 
    LIMIT 5
");
$reviewStmt->execute([$property_id]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดบ้านพัก - <?php echo htmlspecialchars($property['Property_name']); ?></title>
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
            position: relative;
        }

        .back-btn {
            position: absolute;
            left: 2rem;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            padding: 0.75rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            text-decoration: none;
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

        .property-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .property-main {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
            overflow: hidden;
        }

        .property-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
        }

        .property-content {
            padding: 2rem;
        }

        .property-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1rem;
        }

        .property-location {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .property-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .property-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f8f9ff;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
        }

        .detail-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1e5470;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .detail-info h4 {
            font-size: 0.875rem;
            color: #666;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-info p {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .property-sidebar {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .info-card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
            padding: 2rem;
        }

        .info-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .host-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: #1e5470;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .host-info {
            margin-bottom: 1rem;
        }

        .host-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }

        .host-info p {
            color: #666;
            margin: 0.25rem 0;
            font-size: 0.9rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .rooms-section {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
            padding: 2rem;
            margin-bottom: 3rem;
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

        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .room-card {
            background: #f8f9ff;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e5e5e5;
            transition: all 0.3s ease;
        }

        .room-card:hover {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .room-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .room-number {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e5470;
        }

        .room-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: #10b981;
        }

        .room-details {
            margin-bottom: 1rem;
        }

        .room-details p {
            margin: 0.5rem 0;
            color: #666;
            font-size: 0.9rem;
        }

        .room-amenities {
            background: #e3f2fd;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .room-amenities h5 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #1565c0;
            margin-bottom: 0.5rem;
        }

        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .amenity-tag {
            background: #1e5470;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
        }

        .reviews-section {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
            padding: 2rem;
        }

        .review-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e5e5;
        }

        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .reviewer-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .reviewer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #1e5470;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .rating-stars {
            color: #f5b301;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e5e5;
        }

        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
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
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .btn-reject {
            background: #ef4444;
            color: white;
        }

        .btn-reject:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        @media (max-width: 1024px) {
            .property-grid {
                grid-template-columns: 1fr;
            }

            .property-details {
                grid-template-columns: 1fr;
            }

            .rooms-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 1rem;
            }

            .page-header {
                padding: 2rem 1rem;
            }

            .back-btn {
                position: static;
                transform: none;
                margin-bottom: 1rem;
                align-self: flex-start;
            }

            .property-title {
                font-size: 1.5rem;
            }

            .action-buttons {
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
                <li><a href="approve-properties.php" title="อนุมัติสถานที่พัก" class="active"><i
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
                <li><a href="violations.php" title="จัดการเรื่องร้องเรียน"><i
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
                    <a href="approve-properties.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i> กลับ
                    </a>
                    <h1><i class="fas fa-home"></i> รายละเอียดบ้านพัก</h1>
                    <p><?php echo htmlspecialchars($property['Property_name']); ?></p>
                </div>

                <div class="property-grid">
                    <!-- Main Property Info -->
                    <div class="property-main">
                        <?php if (!empty($property['Property_image'])): ?>
                            <img src="../../public/<?php echo htmlspecialchars($property['Property_image']); ?>"
                                alt="<?php echo htmlspecialchars($property['Property_name']); ?>" class="property-image">
                        <?php endif; ?>

                        <div class="property-content">
                            <h2 class="property-title"><?php echo htmlspecialchars($property['Property_name']); ?></h2>

                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                จ.<?php echo htmlspecialchars($property['Property_province']); ?>
                                อ.<?php echo htmlspecialchars($property['Property_district']); ?>
                                ต.<?php echo htmlspecialchars($property['Property_subdistrict']); ?>
                            </div>

                            <?php if (!empty($property['Property_description'])): ?>
                                <div class="property-description">
                                    <?php echo nl2br(htmlspecialchars($property['Property_description'])); ?>
                                </div>
                            <?php endif; ?>

                            <div class="property-details">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-bed"></i>
                                    </div>
                                    <div class="detail-info">
                                        <h4>จำนวนห้อง</h4>
                                        <p><?php echo $property['room_count']; ?> ห้อง</p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="detail-info">
                                        <h4>ราคาเริ่มต้น</h4>
                                        <p>฿<?php echo number_format($property['Property_price']); ?> / คืน</p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="detail-info">
                                        <h4>คะแนนรีวิว</h4>
                                        <p><?php echo $property['avg_rating'] ? number_format($property['avg_rating'], 1) : 'ยังไม่มี'; ?>
                                            (<?php echo $property['review_count']; ?> รีวิว)</p>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-map-pin"></i>
                                    </div>
                                    <div class="detail-info">
                                        <h4>พิกัด</h4>
                                        <p><?php echo htmlspecialchars($property['Property_latitude']); ?>,
                                            <?php echo htmlspecialchars($property['Property_longitude']); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar Info -->
                    <div class="property-sidebar">
                        <!-- Status Card -->
                        <div class="info-card">
                            <h3><i class="fas fa-info-circle"></i> สถานะ</h3>
                            <div class="status-badge status-<?php echo $property['Property_status']; ?>">
                                <i
                                    class="fas fa-<?php echo $property['Property_status'] === 'pending' ? 'clock' : ($property['Property_status'] === 'approved' ? 'check' : 'times'); ?>"></i>
                                <?php
                                $status_text = [
                                    'pending' => 'รอการอนุมัติ',
                                    'approved' => 'อนุมัติแล้ว',
                                    'rejected' => 'ถูกปฏิเสธ'
                                ];
                                echo $status_text[$property['Property_status']] ?? $property['Property_status'];
                                ?>
                            </div>
                            <p><strong>วันที่ลงทะเบียน:</strong>
                                <?php echo date('d/m/Y H:i', strtotime($property['Property_created_at'])); ?></p>
                        </div>

                        <!-- Host Info Card -->
                        <div class="info-card">
                            <h3><i class="fas fa-user"></i> ข้อมูลเจ้าของ</h3>
                            <div class="host-avatar">
                                <?php echo strtoupper(substr($property['Host_firstname'], 0, 1)); ?>
                            </div>
                            <div class="host-info">
                                <h4><?php echo htmlspecialchars($property['Host_firstname'] . ' ' . $property['Host_lastname']); ?>
                                </h4>
                                <p><i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($property['Host_email']); ?></p>
                                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($property['Host_phone']); ?>
                                </p>
                                <p><i class="fas fa-id-card"></i>
                                    <?php echo htmlspecialchars($property['Host_IdCard']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rooms Section -->
                <?php if (count($rooms) > 0): ?>
                    <div class="rooms-section">
                        <h3 class="section-title">
                            <i class="fas fa-bed"></i>
                            ห้องพัก (<?php echo count($rooms); ?> ห้อง)
                        </h3>
                        <div class="rooms-grid">
                            <?php foreach ($rooms as $room): ?>
                                <div class="room-card">
                                    <div class="room-header">
                                        <div class="room-number">ห้อง <?php echo htmlspecialchars($room['Room_number']); ?>
                                        </div>
                                        <div class="room-price">฿<?php echo number_format($room['Room_price']); ?></div>
                                    </div>
                                    <div class="room-details">
                                        <p><strong>ประเภท:</strong> <?php echo htmlspecialchars($room['Room_capacity']); ?></p>
                                        <p><strong>สถานะ:</strong> <?php echo htmlspecialchars($room['Room_status']); ?></p>
                                    </div>
                                    <?php if (!empty($room['Room_utensils'])): ?>
                                        <div class="room-amenities">
                                            <h5>สิ่งอำนวยความสะดวก</h5>
                                            <div class="amenities-list">
                                                <?php
                                                $amenities = explode(',', $room['Room_utensils']);
                                                foreach ($amenities as $amenity):
                                                    ?>
                                                    <span class="amenity-tag"><?php echo trim(htmlspecialchars($amenity)); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Reviews Section -->
                <?php if (count($reviews) > 0): ?>
                    <div class="reviews-section">
                        <h3 class="section-title">
                            <i class="fas fa-star"></i>
                            รีวิวล่าสุด (<?php echo count($reviews); ?> รีวิว)
                        </h3>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <?php echo strtoupper(substr($review['Firstname'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h4><?php echo htmlspecialchars($review['Firstname'] . ' ' . $review['Lastname']); ?>
                                            </h4>
                                            <p><?php echo date('d/m/Y', strtotime($review['Review_date'])); ?></p>
                                        </div>
                                    </div>
                                    <div class="rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?php echo $i <= $review['Rating'] ? '' : '-o'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($review['Review_comment'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <?php if ($property['Property_status'] === 'pending'): ?>
                    <div class="action-buttons">
                        <form method="POST" action="../../controls/approve_property.php?id=<?php echo $property_id; ?>"
                            style="display: inline;">
                            <button class="btn btn-approve" name="approve"
                                onclick="return confirm('คุณต้องการอนุมัติบ้านพักนี้หรือไม่?')">
                                <i class="fas fa-check"></i> อนุมัติบ้านพัก
                            </button>
                        </form>
                        <form method="POST" action="../../controls/approve_property.php?id=<?php echo $property_id; ?>"
                            style="display: inline;">
                            <button class="btn btn-reject" name="cancel"
                                onclick="return confirm('คุณต้องการปฏิเสธบ้านพักนี้หรือไม่?')">
                                <i class="fas fa-times"></i> ปฏิเสธบ้านพัก
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
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