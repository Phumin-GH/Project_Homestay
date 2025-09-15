<?php
session_start();
if (!isset($_SESSION["User_email"])) {
    header("Location: ../index.php");
    exit();
}
include __DIR__ . '/../api/get_bookings.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Homestay Booking</title>
    <link rel="website icon" type="png" href="/images/logo.png">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .bookings-container {
            max-width: 155rem;
            margin: 0 0.5rem;
            padding: 2rem;
        }

        .page-header {
            background: #1e5470;
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
            flex: 0 0 300px;
            /* Fixed width for the image container */
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

        .refund-btn {
            background-color: #6c757d;
            color: white;
        }

        .refund-btn:hover {
            background-color: #5a6268;
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
                <li><a href="main-menu.php"><i class="fas fa-home"></i><span class="menu-label">Home</span></a></li>
                <li><a href="profile.php"><i class="fas fa-user"></i><span class="menu-label">Profile</span></a></li>
                <li><a href="favorites.php"><i class="fas fa-heart"></i><span class="menu-label">Favorite</span></a>
                </li>
                <li><a href="bookings.php" class="active"><i class="fas fa-calendar"></i><span
                            class="menu-label">Booking</span></a></li>
                <li><a href="bookings.php"><i class="fas fa-star"></i><span class="menu-label">Review</span></a></li>
                <li><a href="../controls/logout.php"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['User_email']); ?></span>
                </div>
            </div>
        </aside>

        <div class="main-with-sidebar">
            <?php

            if ($errmsg):
            ?>
                <div class="empty-state">
                    <i class="fa-solid fa-shop-slash"></i>
                    <h3>ไม่สามารถสร้าง QR Code ได้</h3>
                    <p><strong>สาเหตุ:</strong> <?php echo htmlspecialchars($errmsg); ?></p>
                    <a href="main-menu.php">กลับสู่หน้าหลัก</a>
                </div>
            <?php
            else:
            ?>
                <div class="bookings-container">
                    <div class="page-header">
                        <h1><i class="fas fa-calendar-alt"></i> My Bookings</h1>
                        <p>Track your homestay reservations and upcoming stays</p>
                    </div>
                    <div class="auth-tabs">
                        <div class="tab active" id="bookings-tab">
                            <i class="fas fa-sign-in-alt"></i>Host active
                        </div>
                        <div class="tab" id="history-tab">
                            <i class="fa-solid fa-users-line"></i> Host pending
                        </div>

                    </div>
                    <?php if (count($bookings) > 0): ?>
                        <section class="booking-list active" id="bookings-section">
                            <?php foreach ($bookings as $booking): ?>
                                <div class="bookings-grid">
                                    <!-- Example Booking Card 1 (Paid) -->
                                    <div class="booking-card">
                                        <div class="booking-image">
                                            <img src="../<?php echo htmlspecialchars($booking['Property_image']); ?>"
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
                                                    <span
                                                        class="status-badge paid"><?php echo $booking['Booking_status'] == "successful" ? 'ชำระเงินเรียบร้อย' : 'ยังไม่ชำระเงิน'; ?></span>
                                                    <span
                                                        class="status-badge paid"><?php echo $booking['Check_status'] == "Pending" ? 'รอ Check-in' : 'เกิดข้อผิดพลาด'; ?></span>
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
                                                <a href="tel:<?php echo htmlspecialchars($booking['Host_phone']); ?>"
                                                    class="action-btn contact-btn"><i class="fas fa-phone"></i> Contact</a>
                                                <button class="action-btn cancel-btn"><i class="fas fa-times"></i> Cancel</button>
                                                <button class="action-btn refund-btn"><i class="fa-solid fa-rotate"></i>
                                                    Refund</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </section>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No bookings yet</h3>
                            <p>Start exploring homestays and make your first booking to see them here.</p>
                            <a href="main-menu.php" class="browse-btn">Browse Homestays</a>
                        </div>
                    <?php endif; ?>
                    <?php if (count($history_booking) > 0): ?>
                        <section class="booking-list active" id="history-bookings-section" style="display: none;">
                            <?php foreach ($history_booking as $booking): ?>
                                <div class="bookings-grid">
                                    <!-- Example Booking Card 1 (Paid) -->
                                    <div class="booking-card">
                                        <div class="booking-image">
                                            <img src="../<?php echo htmlspecialchars($booking['Property_image']); ?>"
                                                alt="<?php echo htmlspecialchars($booking['Property_name']); ?>">
                                        </div>
                                        <div class="booking-info">
                                            <div class="booking-header">
                                                <h3 class="booking-title"><?php echo htmlspecialchars($booking['Property_name']); ?>
                                                </h3>
                                                <div class="status-badges">
                                                    <span class="status-badge date">ชำระเงิน:
                                                        <?php echo date('d/m/Y', strtotime($booking['Create_at'])); ?></span>
                                                    <span
                                                        class="status-badge paid"><?php echo $booking['Booking_status'] == "successful" ? 'ชำระเงินเรียบร้อย' : 'ยังไม่ชำระเงิน'; ?></span>
                                                    <span
                                                        class="status-badge paid"><?php echo $booking['Check_status'] == "Checked_out" ? 'Check-out แล้ว' : 'เกิดข้อผิดพลาด'; ?></span>
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
                                                <a href="tel:<?php echo htmlspecialchars($booking['Host_phone']); ?>"
                                                    class="action-btn contact-btn"><i class="fas fa-phone"></i> Contact</a>
                                                <button class="action-btn cancel-btn"><i class="fas fa-times"></i> Cancel</button>
                                                <button class="action-btn refund-btn"><i class="fas fa-sync"></i> Refund</button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                        </section>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h3>No bookings yet</h3>
                            <p>Start exploring homestays and make your first booking to see them here.</p>
                            <a href="main-menu.php" class="browse-btn">Browse Homestays</a>
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

        function cancelBooking(bookingId) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                // Send AJAX request to cancel booking
                fetch('../controls/cancel_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            booking_id: bookingId
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
        const bookings = document.getElementById('bookings-tab');
        const history = document.getElementById('history-tab');
        const bookingsSection = document.getElementById('bookings-section');
        const historySection = document.getElementById('history-bookings-section');

        bookings.addEventListener('click', function() {
            bookings.classList.add('active');
            history.classList.remove('active');
            bookingsSection.style.display = 'block';
            historySection.style.display = 'none';
        });

        history.addEventListener('click', function() {
            history.classList.add('active');
            bookings.classList.remove('active');
            historySection.style.display = 'block';
            bookingsSection.style.display = 'none';
        });
    </script>
<?php
            endif;
?>
</body>

</html>