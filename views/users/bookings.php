<?php
session_start();
if (!isset($_SESSION["User_email"])) {
    header("Location: user-login.php");
    exit();
}
require_once __DIR__ . '/../../api/get_bookings.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการจอง - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .bookings-container {
            max-width: 155rem;
            margin: 0 5rem;
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

        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(450px, 2fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .booking-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e5e5;
            transition: all 0.3s ease;
            position: relative;
        }

        .booking-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .booking-card img {
            width: 35rem;
            height: 25rem;
            object-fit: cover;
        }

        .booking-info {
            padding: 1.5rem;
        }

        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .booking-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a1a1a;
            line-height: 1.4;
            flex: 1;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-left: 1rem;
        }

        .host-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .location-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
        }

        .location-info i {
            color: #1e5470;
        }

        .booking-dates {
            background: #f8f9ff;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid #e5e5e5;
        }

        .date-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .date-row:last-child {
            margin-bottom: 0;
        }

        .date-label {
            font-weight: 500;
            color: #666;
            font-size: 0.875rem;
        }

        .date-value {
            font-weight: 600;
            color: #1a1a1a;
            font-size: 0.875rem;
        }

        .booking-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            text-align: center;
            padding: 0.75rem;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e5e5e5;
        }

        .detail-label {
            font-size: 0.75rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 1rem;
            font-weight: 600;
            color: #1a1a1a;
        }

        .booking-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            border: 1px solid #e5e5e5;
        }

        .empty-state i {
            font-size: 4rem;
            color: #e1e5e9;
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #666;
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 2rem;
        }

        .browse-btn {
            background: #1e5470;
            color: white;
            text-decoration: none;
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .browse-btn:hover {
            background: #1e5470;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .bookings-container {
                padding: 1rem;
                margin: 0 0.5rem;
            }

            .page-header {
                padding: 2rem 1rem;
                margin-bottom: 2rem;
            }

            .page-header h1 {
                font-size: 2rem;
            }

            .bookings-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .booking-details {
                grid-template-columns: 1fr;
            }

            .booking-actions {
                flex-direction: column;
            }

            .booking-image {
                flex: 0 0 150px;
            }

            .booking-info {
                padding: 0.5rem;
            }
        }

        @media (min-width: 1200px) {
            .booking-grid {
                grid-template-columns: repeat(5, 1fr);

            }
        }

        /* --- Booking List Styles --- */
        .bookings-grid {
            display: grid;
            /* grid-template-columns: 1fr; */
            grid-template-rows: 1fr;
            gap: 1.5rem;
        }

        .booking-card {
            display: flex;
            background: #ffffff;
            border: 1px solid #e9ecef;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .booking-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.12);
        }

        .booking-image {
            flex: 0 0 400px;

        }

        .booking-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .booking-info {
            flex: 1;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .booking-header {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .booking-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            color: #1a1a1a;
        }

        .status-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            /* Pill shape */
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-badge.date {
            background: #e9ecef;
            color: #495057;
        }

        .status-badge.paid {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.waitpaid {
            background: #f6eac2ff;
            color: #eab60cff;
        }

        .host-info,
        .location-info {
            font-size: 0.9rem;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .booking-dates {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .date-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .date-value {
            font-size: 0.9rem;
            color: #212529;
            font-weight: 600;
        }

        .booking-details {
            display: flex;
            gap: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
        }

        .detail-label {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
        }

        .detail-value {
            font-size: 0.9rem;
            color: #212529;
            font-weight: 600;
        }

        .booking-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: auto;
            /* Pushes actions to the bottom */
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

        .action-btn:hover {
            transform: translateY(-1px);
        }

        .contact-btn {
            background-color: #1e5470;
            color: white;
        }

        .contact-btn:hover {
            background-color: #2a6f97;
        }

        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }

        .cancel-btn:hover {
            background-color: #c82333;
        }

        .views-btn {
            background-color: #2a6f97;
            color: white;

        }

        .views-btn:hover {
            background-color: #2a6f97;
        }

        .refund-btn {
            background-color: #6c757d;
            color: white;
            cursor: not-allowed;
        }

        .refund-btn:hover {
            background-color: #5a6268;
        }

        .disabled-btn {
            background-color: #e9ecef;
            color: #6c757d;
            border: solid 2px #333;
            border-radius: 8px;
            padding: 0.4rem 0.8rem;
        }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .search-box {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box input,
            .search-box button {
                width: 100%;
            }

            .booking-card {
                flex-direction: column;
            }

            .booking-image {
                flex-basis: 200px;
                /* Fixed height for image on mobile */
            }
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

        .modal {
            display: none;
            /* เริ่มต้นซ่อน */
            position: fixed;
            z-index: 500;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 400px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 24px;
            cursor: pointer;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        textarea:focus {
            border: #1e5470 2px solid;
            box-shadow: 0 0 6px rgba(89, 166, 254, 1);
            outline: none;
        }

        button {
            margin-top: 10px;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #1e5470;
            color: #fff;
        }

        button#closeBtn {
            background-color: #ccc;
            margin-left: 10px;
        }

        /* Modal Styles */
        .modal-info {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-contents {
            background-color: #ffffff;
            margin: 10% auto;
            padding: 0;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease-out;
            overflow: hidden;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-header {
            background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .close {
            color: white;
            font-size: 2rem;
            font-weight: bold;
            cursor: pointer;
            border: none;
            background: none;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: all 0.2s ease;
        }

        .close:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 2rem;

        }

        .modal-body .greeting {
            /* text-align: center; */
            font-size: 2rem;
            color: #1e5470;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .modal-body .message {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.6;
            align-content: start;
            margin-bottom: 1.5rem;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        .modal-footer {
            padding: 1rem 2rem 2rem;
            text-align: center;
        }

        .btn-modal {
            background: #1e5470;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-modal:hover {
            background: #74adc9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(30, 84, 112, 0.3);
        }

        @media (max-width: 768px) {
            .modal-content {
                margin: 20% auto;
                width: 95%;
            }

            .modal-header {
                padding: 1rem 1.5rem;
            }

            .modal-body {
                padding: 1.5rem;
            }

            .modal-body .greeting {
                font-size: 1.5rem;
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
                <li><a href="main-menu.php" title="หน้าหลัก"><i class="fa-solid fa-house"></i><span
                            class="menu-label">หน้าหลัก</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">ข้อมูลผู้ใช้งาน</span></a></li>

                <li><a href="favorites_rl.php" title="รายการโปรด"><i class="fas fa-heart"></i><span
                            class="menu-label">รายการโปรด</span></a>
                </li>
                <li><a href="bookings.php" class="active"><i class="fas fa-calendar"></i><span class="menu-label"
                            title="รายการจอง">รายการจอง</span></a>
                </li>

                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">ออกจากระบบ</span></a>
                </li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"
                        title="อีเมล"><?php echo htmlspecialchars($_SESSION['User_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <?php
            if ($errmsg):
            ?>
                <div class="empty-state">
                    <i class="fa-solid fa-shop-slash"></i>
                    <h3>ไม่พบรายการจองของคุณ</h3>
                    <p><strong>สาเหตุ:</strong> <?php echo htmlspecialchars($errmsg); ?></p>
                    <a href="main-menu.php" class="browse-btn">กลับสู่หน้าหลัก</a>
                </div>
            <?php
            else:
            ?>
                <div class="bookings-container">
                    <div class="page-header">
                        <h1><i class="fas fa-calendar-alt"></i> รายการจองทั้งหมด</h1>
                        <p>ติดตามการจองและยกเลิกการจองของคุณ</p>
                    </div>
                    <div class="auth-tabs">
                        <div class="tab active" id="bookings-tab">
                            <i class="fa-solid fa-book-bookmark"></i> รายการจอง
                        </div>
                        <div class="tab" id="history-tab">
                            <i class="fa-solid fa-clock-rotate-left"></i> ประวัติการจอง
                        </div>
                        <div class="tab" id="cancel-tab">
                            <i class="fa-solid fa-ban"></i> ยกเลิกการจอง
                        </div>
                        <div class="tab" id="refund-tab">
                            <i class="fa-solid fa-money-bill-transfer"></i> สถานะการคืนเงิน
                        </div>

                    </div>
                    <section class="booking-list active" id="bookings-section">
                        <?php if (count($bookings) > 0): ?>
                            <?php foreach ($bookings as $booking): ?>
                                <div class="bookings-grid">
                                    <!-- Example Booking Card 1 (Paid) -->
                                    <div class="booking-card">
                                        <div class="booking-image">
                                            <img src="../../public/<?php echo htmlspecialchars($booking['Property_image']); ?>"
                                                alt="<?php echo htmlspecialchars($booking['Property_name']); ?>">
                                        </div>
                                        <div class="booking-info">
                                            <div class="booking-header">
                                                <h3 class="booking-title">
                                                    <?php echo htmlspecialchars($booking['Property_name']); ?>
                                                </h3>
                                                <div class="status-badges">
                                                    <span
                                                        class="status-badge date"><?php echo $booking['Booking_status'] == "successful" ? 'ชำระเงินเรียบร้อย :' : 'ยังไม่ชำระเงิน :'; ?>
                                                        <?php echo date('d/m/Y', strtotime($booking['Create_at'])); ?></span>

                                                    <span
                                                        class="status-badge paid"><?php echo $booking['Check_status'] == "Pending" ? 'รอเข้าพัก' : 'เกิดข้อผิดพลาด'; ?></span>
                                                </div>
                                            </div>
                                            <div class="host-info"><i
                                                    class="fas fa-user"></i><?php echo htmlspecialchars($booking['Host_firstname'] . ' ' . $booking['Host_lastname']); ?>
                                            </div>
                                            <div class="location-info"><i
                                                    class="fas fa-map-marker-alt"></i>จ.<?php echo htmlspecialchars($booking['Property_province']); ?>,
                                                อ.<?php echo htmlspecialchars($booking['Property_district']); ?>,
                                                ต.<?php echo htmlspecialchars($booking['Property_subdistrict']); ?></div>
                                            <div class="booking-dates">
                                                <div>
                                                    <div class="date-label">Check-in</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_in'])); ?></div>
                                                </div>
                                                <div>
                                                    <div class="date-label">Check-out</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_out'])); ?></div>
                                                </div>
                                            </div>
                                            <div class="booking-details">
                                                <div>
                                                    <div class="detail-label">Gusts</div>
                                                    <div class="detail-value"><?php echo $booking['Guests']; ?></div>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Total Price</div>
                                                    <div class="detail-value">
                                                        ฿<?php echo number_format($booking['Total_price']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="booking-actions">
                                                <button class="action-btn cancel-btn"
                                                    data-booking-id="<?php echo htmlspecialchars($booking['Booking_id']); ?>">
                                                    <i class="fas fa-times" type="button"></i> ยกเลิก
                                                </button>
                                                <form method="post" action="detail_house.php" style="display:inline;">
                                                    <input type="hidden" name="house_id"
                                                        value="<?php echo htmlspecialchars($booking["Property_id"]); ?>">
                                                    <button type="submit" class="action-btn views-btn">
                                                        <i class="fas fa-sync"></i>ดูรายละเอียด
                                                    </button>
                                                </form>
                                                <button type="button" onclick="showInfoModal()" class="action-btn views-btn">
                                                    <i class="fas fa-info-circle"></i>นโยบายการคืนเงิน
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>ไม่พบรายการจองของคุณ</h3>
                                <p>เริ่มค้นหาที่พักแบบโฮมสเตย์และทำการจองครั้งแรกของคุณเพื่อดูที่นี่</p>
                                <a href="main-menu.php" class="browse-btn">กลับสู่หน้าหลัก</a>
                            </div>
                        <?php endif; ?>
                    </section>

                    <section class="booking-list active" id="history-bookings-section" style="display: none;">
                        <?php if (count($history_booking) > 0): ?>
                            <?php foreach ($history_booking as $booking): ?>
                                <div class="bookings-grid">
                                    <!-- Example Booking Card 1 (Paid) -->
                                    <div class="booking-card">
                                        <div class="booking-image">
                                            <img src="../../public/<?php echo htmlspecialchars($booking['Property_image']); ?>"
                                                alt="<?php echo htmlspecialchars($booking['Property_name']); ?>">
                                        </div>
                                        <div class="booking-info">
                                            <div class="booking-header">
                                                <h3 class="booking-title"><?php echo htmlspecialchars($booking['Property_name']); ?>
                                                </h3>
                                                <div class="status-badges">
                                                    <span
                                                        class="status-badge date"><?php echo $booking['Booking_status'] == "successful" ? 'ชำระเงินเรียบร้อย :' : 'ยังไม่ชำระเงิน :'; ?>
                                                        <?php echo date('d/m/Y', strtotime($booking['Create_at'])); ?></span>
                                                    <span
                                                        class="status-badge paid"><?php echo $booking['Check_status'] == "Checked_out" ? 'เข้าพักแล้ว' : 'เกิดข้อผิดพลาด'; ?></span>
                                                </div>
                                            </div>
                                            <div class="host-info"><i
                                                    class="fas fa-user"></i><?php echo htmlspecialchars($booking['Host_firstname'] . ' ' . $booking['Host_lastname']); ?>
                                            </div>
                                            <div class="location-info"><i
                                                    class="fas fa-map-marker-alt"></i>จ.<?php echo htmlspecialchars($booking['Property_province']); ?>,
                                                อ.<?php echo htmlspecialchars($booking['Property_district']); ?>,
                                                ต.<?php echo htmlspecialchars($booking['Property_subdistrict']); ?></div>
                                            <div class="booking-dates">
                                                <div>
                                                    <div class="date-label">Check-in</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_in'])); ?></div>
                                                </div>
                                                <div>
                                                    <div class="date-label">Check-out</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_out'])); ?></div>
                                                </div>
                                            </div>
                                            <div class="booking-details">
                                                <div>
                                                    <div class="detail-label">Gusts</div>
                                                    <div class="detail-value"><?php echo $booking['Guests']; ?></div>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Total Price</div>
                                                    <div class="detail-value">฿<?php echo number_format($booking['Total_price']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="booking-actions">
                                                <form method="post" action="detail_house.php" style="display:inline;">
                                                    <input type="hidden" name="house_id"
                                                        value="<?php echo htmlspecialchars($booking["Property_id"]); ?>">
                                                    <button type="submit" class="action-btn refund-btn">
                                                        <i class="fas fa-sync"></i>รีวิวบ้านพัก
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times"></i>
                                        <h3>ไม่พบประวัติจองของคุณ</h3>
                                        <p>เริ่มค้นหาที่พักแบบโฮมสเตย์และทำการจองครั้งแรกของคุณเพื่อดูที่นี่</p>
                                        <a href="main-menu.php" class="browse-btn">กลับสู่หน้าหลัก</a>
                                    </div>
                                <?php endif; ?>
                    </section>

                    <section class="booking-list active" id="bookings-cancel-section" style="display: none;">
                        <?php if (count($cancel_booking) > 0): ?>
                            <?php foreach ($cancel_booking  as $booking): ?>
                                <div class="bookings-grid">
                                    <!-- Example Booking Card 1 (Paid) -->
                                    <div class="booking-card">
                                        <div class="booking-image">
                                            <img src="../../public/<?php echo htmlspecialchars($booking['Property_image']); ?>"
                                                alt="<?php echo htmlspecialchars($booking['Property_name']); ?>">
                                        </div>
                                        <div class="booking-info">
                                            <div class="booking-header">
                                                <h3 class="booking-title">
                                                    <?php echo htmlspecialchars($booking['Property_name']); ?>
                                                </h3>
                                                <div class="status-badges">
                                                    <span
                                                        class="status-badge date"><?php echo $booking['Booking_status'] == "successful" ? 'ชำระเงินเรียบร้อย :' : 'ยกเลิกชำระเงิน :'; ?>
                                                        <?php echo date('d/m/Y', strtotime($booking['Create_at'])); ?></span>
                                                    <span
                                                        class="status-badge <?php echo $booking['Payment_gateway'] == "Qrcode" ? 'unpaid' : 'date'; ?>">ชำระเงินผ่าน<?php echo $booking['Payment_gateway'] == "Qrcode" ? 'Qrcode ไม่สามารถขอคืนเงินได้' : 'Credit card :'; ?>
                                                    </span>
                                                    <span
                                                        class="status-badge unpaid"><?php echo $booking['Booking_status'] == "cancel" ? 'ยกเลิกการจอง' : 'การจองสำเร็จ'; ?></span>

                                                </div>
                                            </div>
                                            <div class="host-info"><i
                                                    class="fas fa-user"></i><?php echo htmlspecialchars($booking['Host_firstname'] . ' ' . $booking['Host_lastname']); ?>
                                            </div>
                                            <div class="location-info"><i
                                                    class="fas fa-map-marker-alt"></i>จ.<?php echo htmlspecialchars($booking['Property_province']); ?>,
                                                อ.<?php echo htmlspecialchars($booking['Property_district']); ?>,
                                                ต.<?php echo htmlspecialchars($booking['Property_subdistrict']); ?></div>
                                            <div class="booking-dates">
                                                <div>
                                                    <div class="date-label">Check-in</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_in'])); ?></div>
                                                </div>
                                                <div>
                                                    <div class="date-label">Check-out</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_out'])); ?></div>
                                                </div>
                                            </div>
                                            <div class="booking-details">
                                                <div>
                                                    <div class="detail-label">Gusts</div>
                                                    <div class="detail-value"><?php echo $booking['Guests']; ?></div>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Total Price</div>
                                                    <div class="detail-value">
                                                        ฿<?php echo number_format($booking['Total_price']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if ($booking['Payment_gateway'] === 'Qrcode'): ?>
                                                <div class="booking-actions">
                                                    <button class="action-btn refund-btn"
                                                        data-booking-id="<?php echo htmlspecialchars($booking['Booking_id']); ?>"
                                                        data-amount="<?php echo htmlspecialchars($booking['Total_price']); ?>" disabled>
                                                        <i class="fas fa-sync"></i>ขอคืนเงิน</button>
                                                </div>
                                            <?php else: ?>
                                                <div class="booking-actions">
                                                    <button class="action-btn contact-btn"
                                                        data-booking-id="<?php echo htmlspecialchars($booking['Booking_id']); ?>"
                                                        data-amount="<?php echo htmlspecialchars($booking['Total_price']); ?>">
                                                        <i class="fas fa-sync"></i>ขอคืนเงิน</button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>ไม่พบรายการยกเลิกการจองของคุณ</h3>
                                <p>เริ่มค้นหาที่พักแบบโฮมสเตย์และทำการจองครั้งแรกของคุณเพื่อดูที่นี่</p>
                                <a href="main-menu.php" class="browse-btn">กลับสู่หน้าหลัก</a>
                            </div>
                        <?php endif; ?>
                    </section>

                    <section class="booking-list active" id="bookings-refund-section" style="display: none;">
                        <?php if (count($refund_booking) > 0): ?>
                            <?php foreach ($refund_booking  as $booking): ?>
                                <div class="bookings-grid">
                                    <!-- Example Booking Card 1 (Paid) -->
                                    <div class="booking-card">
                                        <div class="booking-image">
                                            <img src="../../public/<?php echo htmlspecialchars($booking['Property_image']); ?>"
                                                alt="<?php echo htmlspecialchars($booking['Property_name']); ?>">
                                        </div>
                                        <div class="booking-info">
                                            <div class="booking-header">
                                                <h3 class="booking-title">
                                                    <?php echo htmlspecialchars($booking['Property_name']); ?>
                                                </h3>
                                                <div class="status-badges">
                                                    <span class="status-badge date">ชำระเงิน:
                                                        <?php echo date('d/m/Y', strtotime($booking['Create_at'])); ?></span>
                                                    <span class="status-badge date">ขอคืนเงิน:
                                                        <?php echo date('d/m/Y', strtotime($booking['Refund_date'])); ?></span>
                                                    <?php if ($booking['Host_check'] == "pending" && $booking['Refund_status'] == 'pending'): ?>
                                                        <span class="status-badge waitpaid">รออนุมัติ</span>
                                                    <?php elseif ($booking['Host_check'] == "approve" && $booking['Refund_status'] == 'pending'): ?>
                                                        <span class="status-badge waitpaid">รอดำเนินการ</span>
                                                    <?php elseif ($booking['Host_check'] == "approve" && $booking['Refund_status'] == "approve"): ?>
                                                        <span class="status-badge paid">
                                                            <?php echo  $booking['Host_check'] == "approve" && $booking['Refund_status'] == "approve" ? 'สำเร็จ' : 'ไม่สำเร็จ'; ?></span>
                                                    <?php endif; ?>
                                                    <!-- <span
                                            class="status-badge paid"><?php /*echo $booking['Check_status'] == "Pending" ? 'รอ Check-in' : 'เกิดข้อผิดพลาด';*/ ?></span> -->
                                                </div>
                                            </div>
                                            <div class="host-info"><i
                                                    class="fas fa-user"></i><?php echo htmlspecialchars($booking['Host_firstname'] . ' ' . $booking['Host_lastname']); ?>
                                            </div>
                                            <div class="location-info"><i
                                                    class="fas fa-map-marker-alt"></i>จ.<?php echo htmlspecialchars($booking['Property_province']); ?>,
                                                อ.<?php echo htmlspecialchars($booking['Property_district']); ?>,
                                                ต.<?php echo htmlspecialchars($booking['Property_subdistrict']); ?></div>
                                            <div class="booking-dates">
                                                <div>
                                                    <div class="date-label">Check-in</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_in'])); ?></div>
                                                </div>
                                                <div>
                                                    <div class="date-label">Check-out</div>
                                                    <div class="date-value">
                                                        <?php echo date('d/m/Y', strtotime($booking['Check_out'])); ?></div>
                                                </div>
                                            </div>
                                            <div class="booking-details">
                                                <div>
                                                    <div class="detail-label">Gusts</div>
                                                    <div class="detail-value"><?php echo $booking['Guests']; ?></div>
                                                </div>
                                                <div>
                                                    <div class="detail-label">Total Price</div>
                                                    <div class="detail-value">
                                                        ฿<?php echo number_format($booking['Total_price']); ?>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>ไม่พบรายการยกเลิกการจองของคุณ</h3>
                                <p>เริ่มค้นหาที่พักแบบโฮมสเตย์และทำการจองครั้งแรกของคุณเพื่อดูที่นี่</p>
                                <a href="main-menu.php" class="browse-btn">กลับสู่หน้าหลัก</a>
                            </div>
                        <?php endif; ?>
                    </section>
                </div>
        </div>
    </div>
    <!-- Info Modal -->
    <div id="infoModal" class="modal-info">
        <div class="modal-contents">
            <div class="modal-header">
                <h2><i class="fas fa-info-circle"></i> ข้อมูลเพิ่มเติม</h2>
                <button class="close" onclick="closeInfoModal()">&times;</button>
            </div>
            <div class="modal-body">

                <div class="greeting">นโยบายการคืนเงิน</div>
                <p>รายการจองงที่ชำระผ่าน QR Code จะสามาถทำเรื่องขอเงินคืนออนไลน์ได้</p>
                <div id="policy">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-modal" onclick="closeInfoModal()">
                    <i class="fas fa-check"></i> เข้าใจแล้ว
                </button>
            </div>
        </div>
    </div>
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>เหตุผลการยกเลิกการจอง</h2>
            <form id="cancelForm">
                <input type="hidden" id="bookingIdInput">
                <input type="hidden" id="amountInput">
                <label for="reason">กรุณากรอกเหตุผล:</label>
                <textarea id="reason" name="reason" rows="4" placeholder="ระบุเหตุผล..." required></textarea>
                <br>
                <button type="submit">ส่งข้อมูล</button>
                <button type="button" id="closeBtn">ยกเลิก</button>
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
        // Modal Functions
        async function showInfoModal() {
            try {
                const res = await fetch('../../api/get_policy.php');
                const policies = await res.json();
                const msg_policy = document.getElementById('policy');
                msg_policy.innerHTML = '';
                if (policies === 0) {
                    msg_policy.innerHTML = '<div class="message">ไม่พบนโยบายการคืนเงิน</div>';
                    return
                }
                policies.forEach(policy => {
                    const data = document.createElement('div');
                    data.innerHTML = `
                        <div class="message">${policy.Policy_description}</div>
                    `;
                    msg_policy.appendChild(data);
                });
                const modal = document.getElementById('infoModal');
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';

            } catch (error) {
                console.error('เกิดข้อผิดพลาดในข้อมููล:', error);
            }
        }

        function closeInfoModal() {
            const modal = document.getElementById('infoModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('infoModal');
            if (event.target === modal) {
                closeInfoModal();
            }
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeInfoModal();
            }
        });
        document.addEventListener('DOMContentLoaded', function() {

            const cancelButtons = document.querySelectorAll('.action-btn.cancel-btn');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {

                    const bookingId = this.dataset.bookingId;
                    cancelBooking(bookingId);
                });
            });

            function cancelBooking(bookingId) {
                if (confirm('คุณต้องการยกเลิกการจองนี้ใช่ไหม?')) {

                    // alert('Cancel booking ID: ' + bookingId);
                    fetch('../../controls/bookings_room.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: new URLSearchParams({
                                booking_id: bookingId,
                                cancel_btn: true
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error canceling booking: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error canceling booking');
                        });
                }
            }


        })
        const tabs = [
            document.getElementById('bookings-tab'),
            document.getElementById('history-tab'),
            document.getElementById('cancel-tab'),
            document.getElementById('refund-tab')
        ]
        const sections = [
            document.getElementById('bookings-section'),
            document.getElementById('history-bookings-section'),
            document.getElementById('bookings-cancel-section'),
            document.getElementById('bookings-refund-section')
        ]

        function showTab(TabShow, SectionShow) {
            tabs.forEach(tab => tab.classList.remove('active'));
            sections.forEach(section => section.style.display = 'none');

            TabShow.classList.add('active');
            SectionShow.style.display = 'block';
        }
        tabs.forEach((tab, index) => {
            if (tab) {
                tab.addEventListener('click', function() {
                    showTab(tab, sections[index]);
                })
            }
        })
        const modal = document.getElementById("cancelModal");
        const refund_btn = document.querySelectorAll('.contact-btn');
        const span = document.querySelector(".close");
        const closeBtn = document.getElementById("closeBtn");
        const form = document.getElementById("cancelForm");
        refund_btn.forEach(btn => {
            btn.onclick = function() {
                const bookingId = this.dataset.bookingId;
                const amount = this.dataset.amount;
                document.getElementById('bookingIdInput').value = bookingId;
                document.getElementById('amountInput').value = amount;
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

        form.onsubmit = (e) => {
            e.preventDefault();

            const reason = document.getElementById("reason").value.trim();
            const bookingId = document.getElementById('bookingIdInput').value;
            const amount = document.getElementById('amountInput').value;
            // ตรวจสอบว่ามีข้อมูลครบถ้วน
            if (!bookingId || !amount) {
                alert('เกิดข้อผิดพลาด: ไม่พบข้อมูลการจอง');
                return;
            }
            // ส่งข้อมูลไปยัง Server
            fetch('../../controls/submit_refund.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    // ใช้ข้อมูลที่ดึงมาจาก hidden input
                    body: new URLSearchParams({
                        reason: reason,
                        booking_id: bookingId,
                        amount: amount
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        form.reset();
                        modal.style.display = "none";
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                    console.log(data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการขอคืนเงิน');
                });


        };
    </script>
<?php endif; ?>
</body>

</html>