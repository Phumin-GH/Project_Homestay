<?php
session_start();
if (!isset($_SESSION["User_email"])) {
    header("Location: ../index.php");
    exit();
}

include '../config/db_connect.php';

// if (!isset($_GET['id'])) {
//     echo "ไม่พบรหัสบ้านพัก";
//     exit();
// }


include '../controls/get_homestay.php'; // This will set $house, $rooms, $bookings, and $maps_url
if (!empty($house['Property_lat']) && !empty($house['Property_lng'])) {
    $maps_url = "https://www.google.com/maps?q=" . $house['Property_lat'] . "," . $house['Property_lng'] . "&hl=th&z=16&output=embed";
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../style/style.css" />
    <link rel="stylesheet" href="../style/main-menu.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>รายละเอียดที่พัก - <?php echo htmlspecialchars($house['Property_name']); ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f6fafd;
        margin: 0;
        color: #222;
    }

    .container {
        max-width: 85rem;
        margin: 2rem auto;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        padding: 2rem;
    }

    .header {
        display: flex;
        align-items: flex-start;
        gap: 2rem;
    }

    .main-img {
        width: 340px;
        height: 220px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #eee;
    }

    .info {
        flex: 1;
    }

    .info h2 {
        margin: 0 0 0.5rem 0;
        font-size: 2rem;
        font-weight: 600;
    }



    .info .address {
        color: #555;
        margin-bottom: 0.7rem;
    }

    .info .host {
        color: #1a7f37;
        font-weight: 500;
    }

    .info .type {
        color: #888;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }

    .favorite-btn {
        background: none;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        color: #555;
        display: flex;
        align-items: center;
    }

    .favorite-btn i {
        margin-right: 0.4rem;
        transition: color 0.2s;
    }

    .favorite-btn.active i {
        color: #e3342f;

    }


    /* .favorite-btn i:hover {
        color: whitesmoke;
        transform: scale(1.1);

    } */

    /* .favorite-btn.active {
        color: #e74c3c;
    } */

    .favorite-btn:hover {
        background: #e74c3c;
        transform: scale(1.1);
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 500;
        margin: 2rem 0 1rem 0;

    }

    .rooms-list {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.2rem;
    }

    @media (max-width: 600px) {
        .rooms-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 300px) {
        .rooms-list {
            grid-template-columns: 1fr;
        }
    }

    .room-card {
        background: #fafdff;
        border: 1px solid #e0e7ef;
        border-radius: 8px;
        padding: 1rem 1.2rem;
        min-width: 200px;
        flex: 1 1 220px;
        position: relative;
    }

    .room-card.disable {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f5f5f5;
        pointer-events: none;
    }

    .room-card.selected {
        border: 2px solid #1a7f37;
        background: #e6fbe6;
    }

    .room-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.3rem;
    }

    .room-status {
        display: inline-block;
        font-size: 0.9rem;
        padding: 0.2rem 0.7rem;
        border-radius: 12px;
        background: #f2f2f2;
        color: #666;
        margin-bottom: 0.25rem;
    }

    .room-status.Available {
        background: #e6fbe6;
        color: #1a7f37;
    }

    .room-status.Booked {
        opacity: 1;
        background: #ffeaea;
        color: #c0392b;
    }

    .room-status.Closed {
        opacity: 1;
        background: rgb(174, 174, 174);
        color: rgb(90, 90, 90);
    }

    .room-price {
        font-size: 1.1rem;
        color: #1a7f37;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .calendar-section,
    .map-section {
        margin-top: 2rem;
    }

    .calendar {
        background: #fafdff;
        border: 1px solid #e0e7ef;
        border-radius: 8px;
        padding: 1rem;
        max-width: 350px;
    }

    .calendar table {
        width: 100%;
        border-collapse: collapse;
    }

    .calendar th,
    .calendar td {
        text-align: center;
        padding: 0.4rem;
    }

    .calendar .booked {
        background: #ffeaea;
        color: #c0392b;
        border-radius: 4px;
    }

    .calendar .today {
        background: #e6fbe6;
        color: #1a7f37;
        border-radius: 4px;
    }

    .map-frame {
        width: 100%;
        height: 260px;
        border: none;
        border-radius: 10px;
        margin-top: 0.7rem;
    }

    .actions {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
    }

    .book-btn {
        background: #1a7f37;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 0.7rem 2.2rem;
        font-size: 1.1rem;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
    }

    .bookBtn {
        display: flex;
        background: #1a7f37;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 0.7rem 2.2rem;
        font-size: 1.1rem;
        cursor: pointer;
        font-weight: 500;
        transition: background 0.2s;
    }

    .bookBtn:hover {
        background: #fff;
        color: #1a7f37;
        border: 2px solid #1a7f37;
    }

    .booBtn:disabled {
        cursor: not-allowed;
        background: #ffffffff;
        color: #666464ff;
    }



    @media (max-width: 900px) {
        .header {
            flex-direction: column;
            align-items: stretch;
        }

        .main-img {
            width: 100%;
            height: 200px;
        }
    }

    input[type='hidden'] {
        visibility: visible !important;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-content {
        background: white;
        border-radius: 16px;
        padding: 32px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        position: relative;
        animation: modalSlide 0.3s ease-out;
    }

    @keyframes modalSlide {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .modal-title {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 8px;
    }

    .modal-subtitle {
        font-size: 14px;
        color: #666;
    }

    .payment-methods {
        margin-bottom: 32px;
    }

    .payment-button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 16px 20px;
        margin-bottom: 12px;
        border: 2px solid #e5e5e5;
        border-radius: 12px;
        background: white;
        font-size: 16px;
        font-weight: 500;
        color: #333;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
        overflow: hidden;
    }

    .payment-button:hover {
        border-color: #1a7f37;
        background: #f8fbff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26, 127, 55, 0.15);
    }

    .payment-button:active {
        transform: translateY(0);
    }

    .payment-button.selected {
        border-color: #1a7f37;
        background: #1a7f37;
        color: white;
    }

    .payment-button.selected:hover {
        background: #148a3c;
    }

    .payment-button i {
        /* width: 24px;
        height: 24px; */
        font-size: 24px;
        margin-right: 12px;
        opacity: 0.8;
    }

    .selected .payment-button i {
        opacity: 1;
    }

    .action-buttons {
        display: flex;
        gap: 12px;
    }

    .btn {
        flex: 1;
        padding: 16px 24px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn-cancel {
        background: #f5f5f5;
        color: #666;
    }

    .btn-cancel:hover {
        background: #e5e5e5;
        color: #333;
        transform: translateY(-1px);
    }

    .btn-confirm {
        background: #1a7f37;
        color: white;
    }

    .btn-confirm:hover {
        background: #148a3c;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26, 127, 55, 0.3);
    }

    .btn:active {
        transform: translateY(0);
    }

    /* Ripple effect */
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: scale(0);
        animation: ripple 0.6s linear;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Mobile responsive */
    @media (max-width: 480px) {
        .modal-content {
            padding: 24px;
            margin: 16px;
        }

        .modal-title {
            font-size: 22px;
        }

        .payment-button {
            padding: 14px 16px;
            font-size: 15px;
        }

        .btn {
            padding: 14px 20px;
            font-size: 15px;
        }
    }

    /* Remove old modal styles */
    .modal-close {
        display: none;
    }

    .Payment {
        display: none;
    }

    /* .favoriteBtn {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-left: 1rem;
    } */
    .title {
        display: flex;
        align-items: center;
        /* จัดให้อยู่ตรงกลางแนวตั้ง */
        gap: 1rem;
        /* เว้นระยะระหว่าง h2 กับปุ่ม */
        border: 2px solid black;
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
                <li><a href="main-menu.php" class="active" title="หน้าแดชบอร์ด"><i class="fas fa-home"></i><span
                            class="menu-label">Home</span></a></li>
                <li><a href="profile.php" title="ข้อมูลผู้ใช้งาน"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a></li>
                <li><a href="favorites.php" title="รายการสถานที่พักที่ถูกใจ"><i class="fas fa-heart"></i><span
                            class="menu-label">Favorite</span></a></li>
                <li><a href="bookings.php"><i class="fas fa-calendar"></i><span class="menu-label"
                            title="รายการจอง">Bookings</span></a></li>
                <li><a href="reviewa.php" title="รีวิวสถานที่พัก"><i class="fas fa-star"></i><span
                            class="menu-label">Review</span></a></li>
                <li><a href="../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"
                        title="lllll"><?php echo htmlspecialchars($_SESSION['User_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <div class="container">
                <div class="header">
                    <img src="../<?php echo htmlspecialchars($house['Property_image']); ?>" class="main-img"
                        alt="<?php echo htmlspecialchars($house['Property_name']); ?>">
                    <div class="info">
                        <div class="title">

                            <h2><?php echo htmlspecialchars($house['Property_name']); ?></h2>
                            <button class="favorite-btn" id="favoriteBtn" title="เพิ่มในรายการโปรด" onclick=cilckMe()
                                data-room-property="<?php echo $room['Property_id']; ?>"><i
                                    class="fa-solid fa-heart"></i></button>
                        </div>
                        <div class="address"><i class="fa fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($house['Property_province'] . ', ' . $house['Property_district'] . ', ' . $house['Property_province']); ?>
                        </div>
                        <div class="host">โฮสต์:
                            <?php echo htmlspecialchars($house['Host_firstname'] . ' ' . $house['Host_lastname']); ?>
                        </div>
                        <!-- <div class="type">ประเภท: <?php echo htmlspecialchars($house['Property_type'] ?? 'Homestay'); ?>
                            |
                            รองรับ: <?php echo htmlspecialchars($house['Property_capacity'] ?? 'N/A'); ?> คน</div> -->
                    </div>
                </div>
                <!-- รูปภาพหลักของที่พัก -->
                <div style="width:100%; margin:2rem 0 1.5rem 0;">
                    <img src="../<?php echo htmlspecialchars($house['Property_image']); ?>"
                        alt="<?php echo htmlspecialchars($house['Property_name']); ?>"
                        style="width:100%; max-height:400px; object-fit:cover; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
                </div>
                <div class="section-title">ห้องพัก</div>
                <div class="rooms-list" id="roomsList">
                    <?php foreach ($rooms as $room): ?>
                    <div class="room-card <?php echo htmlspecialchars($room['Room_status'] != 0  ? ' disable' : ''); ?>"
                        data-room-id="<?php echo $room['Room_id']; ?>" data-status="<?php echo $room['Room_status']; ?>"
                        data-room-number="<?php echo $room['Room_number']; ?>"
                        data-room-price="<?php echo $room['Room_price']; ?>"
                        data-room-property="<?php echo $room['Property_id']; ?>">
                        <div class="room-title">ห้อง <?php echo htmlspecialchars($room['Room_number']); ?></div>
                        <div
                            class="room-status <?php echo ($room['Room_status'] == 0 ? 'Available' : ($room['Room_status'] == 1 ? 'Booked' : 'Closed')); ?>">
                            <?php echo ($room['Room_status'] == 0 ? "ว่าง" : ($room['Room_status'] == 1 ? "ไม่ว่าง" : "ปิด")); ?>
                        </div>
                        <div class="room-price">฿<?php echo htmlspecialchars($room['Room_price']); ?> / คืน</div>
                        <div>ประเภท: <?php echo htmlspecialchars($room['Room_capacity'] ?? 'Standard'); ?></div>
                        <div>สิ่งอำนวยความสะดวก: <?php echo htmlspecialchars($room['Room_utensils'] ?? 'Standard'); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="booking-map-flex"
                    style="display:flex; gap:2rem; margin-top:2.5rem; align-items:flex-start;">
                    <div class="map-section" style="flex:1; min-width:280px;">
                        <div class="section-title">แผนที่ที่พัก</div>
                        <!-- <iframe class="map-frame" src="<?php /* echo $maps_url; */ ?>" allowfullscreen=""></iframe> -->
                        <?php /*if ($maps_url):*/ ?>
                        <div id="map" style="width:100%; height:660px; border-radius:10px; margin-top:0.7rem;"></div>
                        <?php /*else: */ ?>
                        <!-- <div style="color:#888;">ไม่มีข้อมูลแผนที่</div> -->
                        <?php /* endif;*/ ?>
                    </div>
                    <div class="booking-section" style="flex:1; min-width:320px; max-width:400px;">
                        <!-- <div class="section-title">ปฏิทินการจอง</div>
                        <div class="calendar" id="calendar"></div> -->
                        <div class="booking-form" id="booking-form"
                            style="margin-top:1.5rem; background:#fafdff; border-radius:8px; padding:1.2rem; border:1px solid #e0e7ef;">
                            <p id="bookingSection"></p>
                            <p id="bookingMessage"></p>
                            <div style="margin-bottom:1rem;">
                                <label for="checkin">วันที่เช็คอิน</label><br>
                                <input type="date" id="checkin" name="checkin" min="<?php echo date('Y-m-d'); ?>"
                                    style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #ccc;">
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label for="checkout">วันที่เช็คเอาท์</label><br>
                                <input type="date" id="checkout" name="checkout"
                                    style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #ccc;"
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label for="guests">เลขห้องพัก</label><br>
                                <input type="number" id="room_number" name="room_number" min="1" max="20" value=""
                                    style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #ccc;"
                                    placeholder="Number" readonly>
                                <input type="hidden" id="prices" name="prices" min="1" max="1000" value="">
                                <input type="hidden" id="roomid" name="roomid" min="1" max="1000" value="">
                                <input type="hidden" id="diffDay" name="diffDay" min="1" max="1000" value="">
                                <input type="hidden" id="propertyid" name="propertyid" min="1" max="1000" value="">
                                <input type="hidden" id="total_price_input" name="total_price_input" min="1" value="">
                            </div>
                            <div style="margin-bottom:1rem;">
                                <label for="guests">จำนวนผู้เข้าพัก</label><br>
                                <input type="number" id="guests" name="guests" min="1" max="20" value="1"
                                    style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #ccc;">
                            </div>

                            <h3 style="margin-top:1rem; font-weight:bold;">รวมราคา</h3>
                            <div style="margin-top:1rem; font-weight:bold;">
                                <p id="nights"></p>
                            </div>
                            <p>ราคารวม: <span name="total_price" id="total_price">0.00</span> บาท</p>


                            <!-- <button class="book-btn" name="bookBtn" id="bookBtn" style="width:100%;" onclick="openConfirm_Booking()">จอง</button> -->
                            <button class="book-btn" name="bookBtn" id="bookBtn"
                                onclick="openConfirm_Booking()">จอง</button>

                            <button class="book-btn" name="resetBtn" id="resetBtn">Reset</button>
                        </div>

                    </div>
                </div>
                <!-- <div class="actions">
                    <button class="book-btn" id="bookBtn" disabled>จองห้องพัก</button>
                </div> -->
            </div>
            <div id="Confirm_booking" class="modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title">ขั้นตอนการชำระเงิน</h2>
                        <p class="modal-subtitle">กรุณาเลือกวิธีการชำระเงิน</p>
                    </div>

                    <div class="payment-methods">
                        <button class="payment-button" data-method="credit-card" id="creditCardBtn">
                            <i class="fa-regular fa-credit-card"></i>
                            Credit Card
                        </button>

                        <button class="payment-button" data-method="qrcode" id="qrcodeBtn">
                            <i class="fa-solid fa-qrcode"></i>
                            QR Code
                        </button>
                    </div>

                    <div class="action-buttons">
                        <button class="btn btn-cancel" onclick="closeConfirm_Booking()">ยกเลิก</button>
                        <button class="btn btn-confirm" id="confirmBtn">ยืนยัน</button>
                    </div>
                </div>
            </div>

            <?php
            // --- User Review Section ---
            // ดึงรีวิวจากฐานข้อมูล (review: Property_id, User_name, Review_text, Review_rating, Review_date)
            // $reviews = [];
            // if (isset($property_id)) {
            //     $stmt = $conn->prepare("SELECT * FROM review WHERE Property_id = ? ORDER BY Review_date DESC");
            //     $stmt->execute([$property_id]);
            //     $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // }
            ?>
            <div class="container" style="margin-top:2rem;">
                <div class="section-title">รีวิวจากผู้เข้าพัก</div>
                <?php /*if (count($reviews) > 0):*/ ?>
                <?php /* foreach ($reviews as $review):*/ ?>
                <div style="border-bottom:1px solid #eee; padding:1rem 0;">
                    <div style="font-weight:600; color:#1a7f37;">
                        <?php echo htmlspecialchars($review['Firstname']); ?>
                        <span style="color:#f5b301; font-size:1.1rem; margin-left:0.5rem;">
                            <?php /* for ($i=0; $i < (int)$review['Review_rating']; $i++) echo '★';*/ ?>
                        </span>
                        <span style="color:#888; font-size:0.95rem; margin-left:1rem;">
                            <?php echo date('d/m/Y', strtotime($review['Review_date'])); ?>
                        </span>
                    </div>
                    <div style="margin-top:0.3rem; color:#333;">
                        <?php echo nl2br(htmlspecialchars($review['Review_text'])); ?>
                    </div>
                </div>
                <?php /*endforeach;*/ ?>
                <?php /*else:*/ ?>
                <!-- <div style="color:#888;">ยังไม่มีรีวิวสำหรับที่พักนี้</div> -->
                <?php /*endif;*/ ?>
                <!-- <div style="margin-top:2rem;">
                    <form method="post" action="">
                        <div style="font-weight:500; margin-bottom:0.5rem;">เพิ่มรีวิวของคุณ</div>
                        <textarea name="review_text" rows="3"
                            style="width:100%; border-radius:6px; border:1px solid #ccc; padding:0.7rem; margin-bottom:0.7rem;"
                            required placeholder="เขียนรีวิว..."></textarea>
                        <div style="margin-bottom:0.7rem;">
                            <label>ให้คะแนน: </label>
                            <select name="review_rating" required style="border-radius:4px; padding:0.2rem 0.5rem;">
                                <option value="">เลือก</option>
                                <option value="5">5 ★</option>
                                <option value="4">4 ★</option>
                                <option value="3">3 ★</option>
                                <option value="2">2 ★</option>
                                <option value="1">1 ★</option>
                            </select>
                        </div>
                        <button type="submit" name="submit_review" class="book-btn"
                            style="padding:0.5rem 1.5rem;">ส่งรีวิว</button>
                    </form> -->
                <?php
                // --- Handle Review Submission ---
                // if (isset($_POST['submit_review']) && isset($_SESSION['User_email'])) {
                //     $review_text = trim($_POST['review_text']);
                //     $review_rating = (int)$_POST['review_rating'];
                //     $user_name = $_SESSION['User_email'];
                //     if ($review_text && $review_rating) {
                //         $stmt = $conn->prepare("INSERT INTO review (Property_id, User_name, Review_text, Review_rating, Review_date) VALUES (?, ?, ?, ?, NOW())");
                //         $stmt->execute([$property_id, $user_name, $review_text, $review_rating]);
                //         echo "<meta http-equiv='refresh' content='0'>"; // refresh page to show new review
                //     }
                // }
                ?>
            </div>
        </div>
    </div> <!-- end .main-with-sidebar -->
    </div> <!-- end .layout -->
    <footer>
        <p>&copy; 2024 Homestay Booking. All rights reserved.</p>
    </footer>

    <script>
    // Initialize payment button listeners after DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const rooms = document.querySelectorAll('.room-card');
        let selectedRoomId = null;
        let selectedPropertyId = null;
        let selectedRoomPrice = 0;

        const prices = document.getElementById('prices');
        const room_number = document.getElementById('room_number');
        const roomid = document.getElementById('roomid');
        const property = document.getElementById('propertyid');
        const checkinInput = document.getElementById('checkin');
        const checkoutInput = document.getElementById('checkout');
        const guestsInput = document.getElementById('guests');
        const nightsDisplay = document.getElementById('nights');
        const diffDayDisplay = document.getElementById('diffDay');
        const totalPriceDisplay = document.getElementById('total_price');
        const bookingMessage = document.getElementById('bookingMessage');
        const bookBtn = document.getElementById('bookBtn');
        const total_price = document.getElementById('total_price_input');

        // ตรวจสอบ DOM elements
        if (!bookingMessage || !totalPriceDisplay) {
            console.error('Missing DOM elements:', {
                bookingMessage,
                //bookBtn,
                total_price
            });
            return;
        }

        rooms.forEach(card => {
            if (card.dataset.status !== '0') {
                card.classList.add('disabled');
            }
        });

        rooms.forEach(card => {
            card.addEventListener('click', function() {
                if (card.dataset.status !== '0') return;
                rooms.forEach(c => c.classList.remove('selected'));
                card.classList.add('selected');
                selectedRoomId = card.dataset.roomId;
                selectedPropertyId = card.dataset.roomProperty;
                selectedRoomPrice = parseFloat(card.dataset.roomPrice);
                roomid.value = selectedRoomId;
                property.value = selectedPropertyId;
                prices.value = selectedRoomPrice;
                room_number.value = card.dataset.roomNumber;
                calculatePrice();
            });
        });
        checkinInput.addEventListener('change', function() {
            if (checkinInput.value) {
                // แปลงค่าวันที่เช็คอินเป็น Date object
                let checkinDate = new Date(checkinInput.value);
                // เพิ่ม 1 วัน
                checkinDate.setDate(checkinDate.getDate() + 1);
                // แปลงเป็นรูปแบบ yyyy-mm-dd
                let year = checkinDate.getFullYear();
                let month = String(checkinDate.getMonth() + 1).padStart(2, '0');
                let day = String(checkinDate.getDate()).padStart(2, '0');
                let minCheckoutDate = `${year}-${month}-${day}`;

                // กำหนด min ของวันที่เช็คเอาท์
                checkoutInput.min = minCheckoutDate;

                // ถ้าวันที่เช็คเอาท์ปัจจุบันน้อยกว่าค่านี้ ให้ล้างค่าวันที่เช็คเอาท์
                if (checkoutInput.value < minCheckoutDate) {
                    checkoutInput.value = '';
                }
            }
        });
        [checkinInput, checkoutInput, guestsInput].forEach(el => {
            el.addEventListener('change', calculatePrice);
            el.addEventListener('input', calculatePrice);
        });

        function calculatePrice() {
            console.log('calculatePrice:', {
                selectedRoomId,
                checkin: checkinInput.value,
                checkout: checkoutInput.value,
                guests: guestsInput.value
            });
            if (!selectedRoomId || !checkinInput.value || !checkoutInput.value) {
                totalPriceDisplay.innerText = '0.00';
                total_price.value = '0.00';
                bookingMessage.innerText = 'กรุณาเลือกห้องพักและวันที่เช็คอิน/เช็คเอาท์';
                bookingMessage.style.color = '#c0392b';
                bookingMessage.style.fontWeight = 'bold';
                bookingMessage.style.textAlign = 'center';
                bookingMessage.style.padding = '1rem';
                bookBtn.disabled = true;
                return;
            }

            const checkinDate = new Date(checkinInput.value);
            const checkoutDate = new Date(checkoutInput.value);

            if (isNaN(checkinDate) || isNaN(checkoutDate) || checkoutDate <= checkinDate) {
                nightsDisplay.innerText = 'กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง';
                totalPriceDisplay.innerText = '0.00';
                total_price.value = '0.00';
                bookingMessage.innerText = 'กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง';
                bookingMessage.style.color = '#c0392b';
                bookBtn.disabled = true;
                return;
            }
            const diffTime = checkoutDate - checkinDate;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);
            diffDayDisplay.value = diffDays;
            nightsDisplay.innerText = `จำนวนคืนที่เข้าพัก: ${diffDays} คืน`;
            fetch('../controls/bookings_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded' // หรือ 'application/json' แล้วแปลง body เป็น JSON.stringify(...)
                    },
                    body: new URLSearchParams({
                        room_id: selectedRoomId,
                        check_in_date: checkinInput.value,
                        check_out_date: checkoutInput.value,
                        nights: diffDays,
                        guests: guestsInput.value
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    // แทน success callback
                    console.log('Fetch response:', data);
                    if (data.error) {
                        // จัดการ error
                        totalPriceDisplay.innerText = '0.00';
                        total_price.value = '0.00';
                        bookingMessage.innerText = data.error;
                        bookingMessage.style.color = '#c0392b';
                        bookBtn.disabled = true;
                    } else {
                        // จัดการผลลัพธ์สำเร็จ
                        totalPriceDisplay.innerText = data.total_price;
                        total_price.value = data.total_price;
                        bookingMessage.innerText = data.message || '';
                        bookingMessage.style.color = data.message ? 'blue' : '';
                        bookBtn.disabled = false;
                    }
                })
                .catch(error => {
                    // แทน error callback
                    console.error('Fetch error:', error);
                    totalPriceDisplay.innerText = '0.00';
                    total_price.value = '0.00';
                    bookingMessage.innerText = 'เกิดข้อผิดพลาด: ' + error;
                    bookingMessage.style.color = '#c0392b';
                    bookBtn.disabled = true;
                });
        }



        // // Payment selection functionality
        // let selectedPayment = null;
        // // Payment method selection
        // document.querySelectorAll('.payment-button').forEach(button => {
        //     button.addEventListener('click', function(e) {
        //         // Remove selected class from all buttons

        //         document.querySelectorAll('.payment-button').forEach(btn => {
        //             btn.classList.remove('selected');
        //         });
        //         // Add selected class to clicked button
        //         this.classList.add('selected');

        //         // Add ripple effect
        //         addRipple(this, e);
        //     });
        // });
        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(button => {
            button.addEventListener('click', function(e) {
                addRipple(this, e);
            });
        });

        function addRipple(button, event) {
            const ripple = document.createElement('span');
            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = event.clientX - rect.left - size / 2;
            const y = event.clientY - rect.top - size / 2;
            ripple.classList.add('ripple');
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            button.appendChild(ripple);
            setTimeout(() => {
                ripple.remove();
            }, 600);
        }
        let selectedPayment = null;
        const payButtons = document.querySelectorAll('.payment-button');
        if (payButtons.length === 0) {
            console.error('No payment buttons found!');
        } else {
            payButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // เอา class selected ออกจากทุกปุ่ม
                    payButtons.forEach(btn => btn.classList.remove('selected'));

                    // ใส่ class ให้ปุ่มที่เลือก
                    this.classList.add('selected');

                    // เก็บค่าที่เลือก
                    selectedPayment = this.dataset.method;


                    // Ripple effect
                    addRipple(this, e);
                });
            });
        }
        // payButtons.forEach(btn => {
        //     btn.addEventListener('click', function() {
        //         selectedMethod = btn.dataset.method; // credit-card หรือ qrcode
        //         console.log('Selected method:', selectedMethod);
        //         alert('Selected method: ' + selectedMethod);

        //         // เพิ่ม class selected
        //         payButtons.forEach(b => b.classList.remove('selected'));
        //         btn.classList.add('selected');
        //     });
        // });
        const ConfirmBtn = document.getElementById('confirmBtn');
        ConfirmBtn.addEventListener('click', function(e) {
            //ตรวจสอบ room_id และ วันที่เช็คอิน,วันที่เช็คเอ้าท์ ต้องไม่เป็นค่าว่าง
            if (!selectedRoomId || !checkinInput.value || !checkoutInput.value || !
                selectedPayment) {
                //ถ้าว่างไม่สามารถกดปุ่มจองได้
                ConfirmBtn.disabled = true;
                return;
            }
            //ตรวจสอบ room_id และ วันที่เช็คอิน,วันที่เช็คเอ้าท์ แล้วไม่เป็นค่าว่าง 
            fetch('../controls/bookings_room.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    //ส่งข้อมูลใน body ไปที่โฟลเดอร์ controls/bookings_room.php
                    body: new URLSearchParams({
                        room_id: selectedRoomId,
                        property_id: selectedPropertyId,
                        check_in_date: checkinInput.value,
                        check_out_date: checkoutInput.value,
                        nights: diffDayDisplay.value,
                        guests: guestsInput.value,
                        total_price: total_price.value,
                        submit_btn: true
                    })
                })
                //ผลลัพธ์
                .then(response => {
                    //ตรวจสอบค่าผลลัพธ์ที่เกี่ยวNetwork ถ้ามีปัญหาให้แจ้ง network response was not ok
                    if (!response.ok) throw new Error('network response was not ok');
                    //คืนค่าเป็น json
                    return response.json();
                })
                //รับข้อมูลได้ ไม่มี error
                .then(data => {
                    console.log('Fetch response :', data);
                    //ตรวจสอบผลลัพธ์ เป็นค่า error ไหม
                    if (data.error) {
                        //ถ้าใช่แสดงข้อความสีแดง
                        bookingMessage.innerText = data.error;
                        bookingMessage.style.color = '#c0392b';
                        //กดปุ่มจองและยืนยันการจองไม่ได้
                        ConfirmBtn.disabled = false;
                        bookBtn.disabled = false;
                        document.getElementById('Confirm_booking').style.display =
                            'none';
                        if (data.error === 'กรุณาเข้าสู่ระบบ') {
                            window.location.href = 'user-login.php';
                        }
                    } else {
                        bookingMessage.innerText = data.message ||
                            'ยืนยันการจองสำเร็จ';
                        bookingMessage.style.color = 'green';
                        alert('ยืนยันการจองสำเร็จ');
                        e.preventDefault();
                        const latestTotalPrice = parseFloat(total_price.value);
                        const bookingId = data.booking_id;
                        if (!isNaN(latestTotalPrice) && latestTotalPrice > 0) {
                            fetch('../controls/save_price.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded'
                                    },
                                    body: 'total_price=' + encodeURIComponent(
                                            latestTotalPrice) +
                                        '&booking_id=' + encodeURIComponent(
                                            bookingId) +
                                        '&method=' + encodeURIComponent(
                                            selectedPayment)
                                })
                                .then(response => response.text())
                                .then(data => {
                                    if (data.trim() === "qrcode") {
                                        window.location.href = 'Qrpayment.php';
                                    } else if (data.trim() === "credit-card") {
                                        window.location.href = 'credit-card.php';
                                    } else {
                                        alert("ไม่สามารถบันทึกราคาได้");
                                    }
                                })
                                .catch(err => console.error(err));
                        }
                    }
                })
                //รับข้อมูลไม่ได้ มี error
                .catch(error => {
                    console.error('Failed to parse JSON:', error);
                    bookingMessage.innerText = 'เกิดข้อผิดพลาด: Response ไม่ถูกต้อง';
                    bookingMessage.style.color = '#c0392b';
                    ConfirmBtn.disabled = false;
                    bookBtn.disabled = false;
                    document.getElementById('Confirm_booking').style.display = 'none';
                })
        });
    });

    //reset from การจอง
    function resetForm() {
        checkinInput.value = '';
        checkoutInput.value = '';
        guestsInput.value = '1';
        nightsDisplay.innerText = '';
        totalPriceDisplay.innerText = '0.00';
        total_price.value = '0.00';
        roomid.value = '';
        property.value = '';
        room_number.value = '';
        prices.value = '';
        rooms.forEach(c => c.classList.remove('selected'));
        selectedRoomId = null;
        selectedPropertyId = null;
        selectedRoomPrice = 0;
        bookingMessage.innerText = 'กรุณาเลือกห้องพักและวันที่เช็คอิน/เช็คเอาท์';
        bookingMessage.style.color = '#c0392b';
        bookingMessage.style.fontWeight = 'bold';
        bookingMessage.style.textAlign = 'center';
        bookingMessage.style.padding = '1rem';
        bookBtn.disabled = true;
    }
    //ปุ่มreset form
    const resetBtn = document.getElementById('resetBtn');
    resetBtn.type = 'button';
    resetBtn.innerText = 'Reset';
    resetBtn.onclick = resetForm;
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.appendChild(resetBtn);
    } else {
        console.error('Element with id "booking-form" not found');
    }

    const buttons = document.querySelectorAll(".favorite-btn");

    buttons.forEach(button => {
        button.addEventListener("click", async () => {
            const houseId = button.dataset.propertyId;

            // ส่งข้อมูลไป PHP
            fetch("../controls/favorite.php", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: JSON.stringify({
                        house_id: houseId
                    })
                })
                .then(response => response.json())
                .then(result => {

                    if (result.success) {
                        // toggle icon
                        if (button.classList.contains("active")) {
                            button.classList.remove("active");
                            button.innerHTML = "🤍 Favorite";
                        } else {
                            button.classList.add("active");
                            button.innerHTML = "❤️ Favorited";
                        }
                    } else {
                        alert("เกิดข้อผิดพลาด: " + result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("เกิดข้อผิดพลาด: " + error.message);
                });

        });
    });
    </script>
</body>

</html>
<script>
//เปิด-ปิด Sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.querySelector('.main-with-sidebar');
    sidebar.classList.toggle("collapsed");
    mainContent.classList.toggle("sidebar-collapsed");
}
//เปิดModal ยืนยันการจอง
function openConfirm_Booking() {
    document.getElementById('Confirm_booking').style.display = 'flex';

}
//ปิดModal ยืนยันการจอง
function closeConfirm_Booking() {
    document.getElementById('Confirm_booking').style.display = 'none';
}
//ถ้าClick ส่วนที่ไม่ใช่Modal ให้ปิดModal
window.onclick = function(event) {
    const modal = document.getElementById('Confirm_booking');

    if (event.target === modal) {
        closeConfirm_Booking();
    }
}
</script>

<script>
//สคิปต์ดึงค่าละติจูดและลองจิจูดมาแสดง
const lat = <?php echo isset($house['Property_latitude']) ? floatval($house['Property_latitude']) : 13.7563; ?>;
const lng = <?php echo isset($house['Property_longitude']) ? floatval($house['Property_longitude']) : 100.5018; ?>;
const map = L.map('map').setView([lat, lng], 20);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

L.marker([lat, lng]).addTo(map);
</script>
</body>

</html>