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
    <title>รายละเอียดที่พัก - <?php /*echo htmlspecialchars($house['Property_name']);*/ ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        background: #f6fafd;
        margin: 0;
        color: #222;
    }

    .container {
        max-width: 105rem;
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
        color: #e74c3c;
        font-size: 1.7rem;
        cursor: pointer;
        margin-left: 0.5rem;
    }

    .favorite-btn.active {
        color: #c0392b;
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 500;
        margin: 2rem 0 1rem 0;
    }

    .rooms-list {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.2rem;
    }

    @media (max-width: 900px) {
        .rooms-list {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 600px) {
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
        /* คงความชัดเจนตามที่ระบุ */
        cursor: not-allowed;
        /* เปลี่ยนเคอร์เซอร์เป็นสัญลักษณ์ห้าม */
        background: #f5f5f5;
        /* สีพื้นหลังจางลงเล็กน้อยเพื่อบ่งบอกว่าไม่สามารถเลือกได้ */
        pointer-events: none;
        /* ป้องกันการโต้ตอบ เช่น คลิก */
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
                        <h2><?php echo htmlspecialchars($house['Property_name']); ?>
                            <button class="favorite-btn" id="favoriteBtn" title="เพิ่มในรายการโปรด"><i
                                    class="fa fa-heart"></i></button>
                        </h2>
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
                        <div>ประเภท: <?php  echo htmlspecialchars($room['Room_capacity'] ?? 'Standard');?></div>
                        <div>สิ่งอำนวยความสะดวก: <?php  echo htmlspecialchars($room['Room_utensils'] ?? 'Standard');?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="booking-map-flex"
                    style="display:flex; gap:2rem; margin-top:2.5rem; align-items:flex-start;">
                    <div class="map-section" style="flex:1; min-width:280px;">
                        <div class="section-title">แผนที่ที่พัก</div>
                        <?php /*if ($maps_url):*/ ?>
                        <iframe class="map-frame" src="<?php /* echo $maps_url; */?>" allowfullscreen=""></iframe>
                        <div id="map" style="width:100%; height:260px; border-radius:10px; margin-top:0.7rem;"></div>
                        <?php /*else: */?>
                        <div style="color:#888;">ไม่มีข้อมูลแผนที่</div>
                        <?php /* endif;*/ ?>
                    </div>
                    <div class="booking-section" style="flex:1; min-width:320px; max-width:400px;" id="bookingSection">
                        <div class="section-title">ปฏิทินการจอง</div>
                        <div class="calendar" id="calendar"></div>
                        <form class="booking-form" method="post" action="../controls/book_room.php"
                            style="margin-top:1.5rem; background:#fafdff; border-radius:8px; padding:1.2rem; border:1px solid #e0e7ef;">
                            <p>กรุณาเลือกห้องพักก่อนทำการจอง</p>
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
                                <input type="number" id="room" name="room_number" min="1" max="20" value=""
                                    style="width:100%; padding:0.5rem; border-radius:6px; border:1px solid #ccc;"
                                    readonly>
                                <input type="number" id="price" name="price" min="1" max="1000" value="">
                                <input type="hidden" id="roomid" name="room" min="1" max="1000" value="">
                                <input type="hidden" id="propertyid" name="property" min="1" max="1000" value="">
                                <input type="number" id="totalPrice" name="totalPrice" min="1" max="10000" value="">
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
                            <!-- <div id="totalPrice" style="margin-top:1rem; font-weight:bold;"></div> -->


                            <button onclick="openQrCode()" type="submit" name="submit_book" class="book-btn"
                                style="width:100%;">จอง</button>
                        </form>
                    </div>
                </div>
                <!-- <div class="actions">
                    <button class="book-btn" id="bookBtn" disabled>จองห้องพัก</button>
                </div> -->
            </div>
            <div id="bookingResult" style="margin-top:1rem; color: green;"></div>
            <!-- Modal QR Code -->
            <div id="qrModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close" onclick="closeQrModal()">&times;</span>
                    <h2>QR Code for Payment</h2>
                    <div id="qrCodeContainer" style="text-align:center;"></div>
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
                <?php /*if (count($reviews) > 0):*/?>
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
                <div style="color:#888;">ยังไม่มีรีวิวสำหรับที่พักนี้</div>
                <?php /*endif;*/ ?>
                <div style="margin-top:2rem;">
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
                    </form>
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
    // ฟังก์ชันเปิด Modal
    function openQrModal() {
        document.getElementById('qrModal').style.display = 'block';
    }

    // ฟังก์ชันปิด Modal
    function closeQrModal() {
        document.getElementById('qrModal').style.display = 'none';
    }

    // ดักจับ submit form ด้วย AJAX
    document.querySelector('.booking-form').addEventListener('submit', function(e) {
        e.preventDefault(); // ไม่ให้ reload หน้า

        // เก็บข้อมูลจากฟอร์ม
        const formData = new FormData(this);

        // ส่งข้อมูลด้วย fetch
        fetch('../controls/book_room.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // แสดงข้อความสำเร็จ
                    document.getElementById('bookingResult').style.color = 'green';
                    document.getElementById('bookingResult').textContent =
                        'จองสำเร็จ! โปรดสแกน QR Code เพื่อชำระเงิน';

                    // แสดง QR Code ใน Modal (ตัวอย่างใช้ promptpay.io)
                    const promptpayNumber = data.promptpayNumber || '0812345678'; // รับจาก response
                    const amount = data.amount || formData.get('totalPrice'); // รับจาก response หรือฟอร์ม
                    const qrUrl = `https://promptpay.io/${promptpayNumber}/${amount}.png`;

                    document.getElementById('qrCodeContainer').innerHTML = `
        <img src="${qrUrl}" alt="PromptPay QR" style="width:250px; height:250px;" />
        <p>ยอดชำระ: ฿${amount}</p>
        <p>หมายเลขพร้อมเพย์: ${promptpayNumber}</p>
      `;

                    openQrModal();

                    // เคลียร์ฟอร์มหรือไม่ก็ได้
                    // this.reset();
                } else {
                    document.getElementById('bookingResult').style.color = 'red';
                    document.getElementById('bookingResult').textContent = 'เกิดข้อผิดพลาด: ' + (data
                        .message || 'ไม่สามารถจองได้');
                }
            })
            .catch(err => {
                document.getElementById('bookingResult').style.color = 'red';
                document.getElementById('bookingResult').textContent = 'เกิดข้อผิดพลาดขณะส่งข้อมูล';
                console.error(err);
            });
    });
    </script>

    <script>
    const rooms = document.querySelectorAll('.room-card');
    let selectedRoomId = null;
    let selectedPropertyId = null;
    let selectedRoomPrice = 0;

    const room = document.getElementById('room'); // ต้องมี <input id="room"> ใน HTML
    const price = document.getElementById('price'); // ต้องมี <input id="price"> หรือใส่ไว้ใน form
    const roomid = document.getElementById('roomid'); // ต้องมี <input id="id"> ใน HTML
    const property = document.getElementById('propertyid'); // ต้องมี <input id="propertyid"> ใน HTML


    rooms.forEach(card => {
        card.addEventListener('click', function roomPrices() {
            if (card.dataset.status !== '0') return;

            rooms.forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedPropertyId = card.dataset
                .roomProperty; // กำหนดค่าให้กับ input ที่มี id="propertyid"
            selectedRoomId = card.dataset.roomId;
            selectedRoomPrice = parseFloat(card.dataset.roomPrice);
            roomid.value = selectedRoomId; // กำหนดค่าให้กับ input ที่มี id="roomid"
            property.value = selectedPropertyId; // กำหนดค่าให้กับ input ที่มี id="propertyid"
            room.value = card.dataset.roomNumber;
            price.value = selectedRoomPrice;

            document.getElementById('bookBtn').disabled = false;
            console.log(
                `คุณเลือกห้อง: ${card.dataset.roomNumber} ราคา: ฿${selectedRoomPrice} Room ID: ${selectedRoomId} | Property ID: ${selectedPropertyId}`
            );

            calculateNights(); // เรียกคำนวณราคารวมทันที
        });

    });

    function resetForm() {
        if (checkinInput) checkinInput.value = '';
        if (checkoutInput) checkoutInput.value = '';
        if (nightsDisplay) nightsDisplay.innerText = '';
        if (totalPrice) totalPrice.innerText = '';
        if (bookingSection) {
            bookingSection.innerText = 'กรุณาเลือกห้องพักและวันที่เช็คอิน/เช็คเอาท์';
            bookingSection.style.color = "#c0392b";
            bookingSection.style.fontWeight = "bold";
            bookingSection.style.textAlign = "center";
            bookingSection.style.padding = "1rem";
        }
        if (bookBtn) bookBtn.disabled = true;
    }
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const nightsDisplay = document.getElementById('nights');
    const totalPrice = document.getElementById('totalPrice'); // เพิ่มตัวแปรนี้
    const bookingSection = document.getElementById('bookingSection');



    function calculateNights() {
        if (!checkinInput.value || !checkoutInput.value) {
            return;
        }

        const checkinDate = new Date(checkinInput.value);
        const checkoutDate = new Date(checkoutInput.value);

        if (isNaN(checkinDate) || isNaN(checkoutDate)) {
            nightsDisplay.innerText = "กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง";
            // totalPriceDisplay.innerText = "กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง";
            return;
        }

        if (selectedRoomPrice === 0) {
            bookingSection.innerText = "กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง และเลือกห้องพักก่อนทำการจอง";
            bookingSection.style.color = "#c0392b";
            bookingSection.style.fontWeight = "bold";
            bookingSection.style.textAlign = "center";
            bookingSection.style.padding = "1rem";
            return;
        }

        if (checkoutDate > checkinDate) {
            const diffTime = checkoutDate - checkinDate;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);

            nightsDisplay.innerText = `จำนวนคืนที่เข้าพัก: ${diffDays} คืน`;

            if (selectedRoomPrice > 0) {
                const total = selectedRoomPrice * diffDays;
                //totalPriceDisplay.innerText = `ราคารวม: ฿${total.toLocaleString()}`;
                totalPrice.value = total; // แก้ไขจาก totaolPriceDispplay
            } else {
                totalPrice.value = 0;
            }
        } else {
            nightsDisplay.innerText = "กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง";
            totalPriceDisplay.innerText = "กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง";
        }
    }

    // เพิ่ม event listener สำหรับ price
    price.addEventListener('change', () => {
        const newPrice = parseFloat(price.value);
        if (isNaN(newPrice) || newPrice <= 0) {
            selectedRoomPrice = 0;
            resetForm();
            bookingSection.innerText = "กรุณากรอกราคาที่ถูกต้อง";
            bookingSection.style.color = "#c0392b";
            bookingSection.style.fontWeight = "bold";
            bookingSection.style.textAlign = "center";
            bookingSection.style.padding = "1rem";
            return;
        }
        selectedRoomPrice = newPrice;
        calculateNights();
    });

    checkinInput.addEventListener('change', calculateNights);
    checkoutInput.addEventListener('change', calculateNights);
    </script>
    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-with-sidebar');
        sidebar.classList.toggle("collapsed");
        mainContent.classList.toggle("sidebar-collapsed");
    }

    function openQrCode() {
        document.getElementById('QrCode').style.display = 'flex';
        const modal = document.getElementById
    }
    </script>

    <script>
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