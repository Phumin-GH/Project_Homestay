<?php
session_start();
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}
include '../config/db_connect.php';

$host_email = $_SESSION['Host_email'];

// ดึง host info
$stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ?");
$stmt->execute([$host_email]);
$host = $stmt->fetch(PDO::FETCH_ASSOC);

// ดึง properties ของ host

// $stmt = $conn->prepare("
//     SELECT p.*, r.Room_id, r.Room_number, r.Room_status,r.Room_price
//     FROM  Property p
//     INNER JOIN Room r ON p.Property_id = r.Property_id
//     WHERE p.Host_id = ? AND p.Property_status = '1' AND r.Room_status = '0'
//     ORDER BY p.Property_id DESC
// ");
// $stmt->execute([$host['Host_id']]);
// $properties= $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt = $conn->prepare("
    SELECT  p.Property_id, p.Property_name
    FROM Property p
    WHERE p.Host_id = ? AND p.Property_status = '1'
    ORDER BY p.Property_id DESC
");
$stmt->execute([$host['Host_id']]);
$properties = $stmt->fetchAll(PDO::FETCH_ASSOC);


// $stmt = $conn->prepare("
//     SELECT r.Room_id, r.Room_number, r.Property_id
//     FROM Room r
//     INNER JOIN Property p ON r.Property_id = p.Property_id
//     WHERE p.Property_id = ?  AND p.Property_status = '1'
// ");
// $stmt->execute([$properties[0]['Property_id']]);
// $room = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงห้องพักของ property แรก (หรือแยกดึงตาม property_id ที่เลือก)

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มการจอง Walk-in - Homestay Booking</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .form-container {
        max-width: 750px;
        margin: 2rem auto;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 2rem;
    }

    .form-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #1e5470;
        text-align: center;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .group {
        display: flex;
        gap: 50px;

    }

    .form-label {
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 0.75rem;
        border: 1.5px solid #e5e5e5;
        border-radius: 8px;
        font-size: 1rem;
        box-sizing: border-box;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
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

    .btn-primary {
        background: #1e5470;
        color: white;
    }

    .btn-primary:hover {
        background: #2d7da4ff;
    }

    .btn-secondary {
        background: #f3f4f6;
        color: #1e5470;
        border: 1.5px solid #1e5470;
    }

    .btn-secondary:hover {
        background: #e0e7ff;
    }

    .input-group {
        display: flex;
        gap: 2rem;
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
                <li><a href="host-dashboard.php" title="รายงาน"><i class="fas fa-tachometer-alt"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                            class="menu-label">Manage
                            Property</span></a></li>

                <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                            class="menu-label">Test</span></a></li>
                <li><a href="walkin-property.php" title="การจอง" class="active"><i
                            class="fa-solid fa-person-walking"></i><span class="menu-label">Walkin</span></a></li>
                <li><a href="../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
                            class="menu-label">Logout</span></a></li>
            </ul>
            <div class="sidebar-footer">
                <div>
                    <i class="fas fa-user-circle"></i>
                    <span class="menu-label"><?php echo htmlspecialchars($_SESSION['Host_email']); ?></span>
                </div>
            </div>
        </aside>
        <div class="main-with-sidebar">
            <div class="form-container">
                <div class="form-title"><i class="fas fa-user-plus"></i> เพิ่มการจอง Walk-in</div>
                <form>
                    <h3 id="bookingMessage"></h3>
                    <div class="form-group">
                        <label class="form-label">เลือกบ้านพัก <span style="color:red">*</span></label>
                        <select name="property_id" class="form-select" id="propertySelect" required>
                            <option value="">-- เลือกบ้านพัก --</option>
                            <?php foreach ($properties as $p): ?>
                            <option value="<?= $p['Property_id'] ?>" data-property-id="<?= $p['Property_id'] ?>">
                                <?= htmlspecialchars($p['Property_name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" id="propertyId" name="property_id">
                    </div>
                    <div class="form-group">
                        <label class="form-label">เลือกห้องพัก <span style="color:red">*</span></label>
                        <select name="property_id" class="form-select" id="roomSelect" required>
                            <option value="">-- เลือกห้องพัก --</option>


                        </select>
                    </div>
                    <div class="form-group">
                        <div class="group">

                            <label class="form-label">ชื่อ-นามสกุลผู้เข้าพัก <span style="color:red">*</span></label>

                        </div>
                        <div class="group">

                            <input type="text" name="guest_name" class="form-input" id="first_name" required>
                            <input type="text" name="guest_name" class="form-input" id="last_name" required>

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">เบอร์โทรศัพท์ <span style="color:red">*</span></label>
                        <input type="text" name="guest_phone" id="guests_phone" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">วันที่เช็คอิน&เช็คเอาท์ <span style="color:red">*</span></label>
                        <div class="input-group">
                            <input type="date" name="checkin" id="checkin" class="form-input"
                                min="<?php echo date('Y-m-d'); ?>" required>
                            <input type="date" id="checkout" name="checkout" class="form-input"
                                min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="form-label">จำนวนคืน <span style="color:red">*</span></label>
                        <input type="number" name="total-days" class="form-input" id="total-days" placeholder="0"
                            disabled>
                        <input type="number" name="price" class="form-input" id="price" placeholder="0" disabled>
                        <input type="number" name="roomId" class="form-input" id="roomId" placeholder="0" disabled>
                    </div>
                    <div class="form-group">
                        <label class="form-label">จำนวนผู้เข้าพัก <span style="color:red">*</span></label>
                        <input type="number" name="guests" id="guests" class="form-input" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">ราคารวม (บาท) <span style="color:red">*</span></label>
                        <input type="number" name="total_price" id="total_price" class="form-input" min="0" value="0"
                            required disabled>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" name="submit_btn" id="bookBtn"><i
                                class="fas fa-save"></i> บันทึกการจอง</button>
                        <button type="submit" class="btn btn-secondary" name="reset_btn" id="resetBtn"><i
                                class="fa-solid fa-circle-notch"></i> ล้างข้อมูล</button>
                        <a href="host-dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i>
                            กลับ</a>
                    </div>
                </form>
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


    const total_price = document.getElementById('total_price');
    const roomSelect = document.getElementById('roomSelect');
    const total_days = document.getElementById('total-days');
    const bookingMessage = document.getElementById('bookingMessage');
    const checkinInput = document.getElementById('checkin');
    const checkoutInput = document.getElementById('checkout');
    const price = document.getElementById('price');
    const roomId = document.getElementById('roomId');

    const f_name = document.getElementById('first_name');
    const l_name = document.getElementById('last_name');
    const g_phone = document.getElementById('guests_phone');
    const guests = document.getElementById('guests');
    const propertySelect = document.getElementById('propertySelect');
    const propertyId = document.getElementById('propertyId');
    propertySelect.addEventListener('change', function() {
        const selectedOption = propertySelect.options[propertySelect.selectedIndex];
        propertyId.value = selectedOption.dataset.propertyId || '';
    })
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
    [checkinInput, checkoutInput, guests].forEach(el => {
        el.addEventListener('change', calculateDays);
        el.addEventListener('input', calculateDays);
    });



    function calculateDays() {
        if (!roomId.value || !checkinInput.value || !checkoutInput.value) {

            total_price.value = '0.00';
            bookingMessage.innerText = 'กรุณาเลือกห้องพักและวันที่เช็คอิน/เช็คเอาท์';
            bookingMessage.style.color = '#c0392b';
            bookingMessage.style.fontWeight = 'bold';
            bookingMessage.style.textAlign = 'center';
            bookingMessage.style.padding = '1rem';

        }
        const checkinDate = new Date(checkinInput.value);
        const checkoutDate = new Date(checkoutInput.value);
        if (isNaN(checkinDate) || isNaN(checkoutDate) || checkoutDate <= checkinDate) {


            total_price.value = '0.00';
            bookingMessage.innerText = 'กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง';
            bookingMessage.style.color = '#c0392b';

        }
        const timeDiff = checkoutDate - checkinDate;
        const dayDiff = timeDiff / (1000 * 60 * 60 * 24); // แปลงเป็นจำนวนวัน

        if (dayDiff > 0) {
            total_days.value = dayDiff;
        } else {
            total_days.value = 0;
        }

        bookingMessage.innerText = 'กรุณาเลือกวันที่เช็คอินและเช็คเอาท์ที่ถูกต้อง';
        bookingMessage.style.color = '#c0392b';

        fetch('../controls/books_room.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    room_id: roomId.value,
                    check_in_date: checkinInput.value,
                    check_out_date: checkoutInput.value,
                    nights: dayDiff,
                    guests: guests.value,
                    price: price.value
                })
            })

            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Fetch response :', data);
                if (data.error) {
                    total_price.value = '0.00';
                    bookingMessage.innerText = data.error;

                    bookingMessage.style.color = '#c0392b';
                    alert('Error' + data.error);
                } else {
                    total_price.value = data.total_price;
                    bookingMessage.innerText = data.message || '';
                    bookingMessage.style.color = data.message ? 'blue' : '';
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                bookingMessage.innerText = 'เกิดข้อผิดพลาด: ' + error;
                bookingMessage.style.color = '#c0392b';
                total_price.value = '0.00';
                alert('Error' + error);
            });

    }
    const bookBtn = document.getElementById('bookBtn');
    bookBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (!roomId.value || !checkinInput.value || !checkoutInput.value || !f_name.value || !l_name.value ||
            !g_phone.value) {
            console.log('Button disabled because some inputs are empty');
            alert('Button disabled because some inputs are empty');

        }
        fetch('../controls/books_room.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    room_id: roomId.value,
                    property_id: propertyId.value,
                    check_in_date: checkinInput.value,
                    check_out_date: checkoutInput.value,
                    firstName: f_name.value,
                    lastName: l_name.value,
                    guestsPhone: g_phone.value,
                    nights: total_days.value,
                    total_price: total_price.value,
                    guests: guests.value,
                    submit_btn: true
                })
            })

            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log('Fetch response :', data);
                if (data.success) {
                    bookingMessage.innerText = data.message;
                    bookingMessage.style.color = 'green';
                    alert('จองสำเร็จ');
                } else {

                    // bookBtn.disabled = true;
                    bookingMessage.innerText = data.error;
                    bookingMessage.style.color = '#c0392b';
                    alert('จองไใ่ได้');
                }
            })
            .catch(error => {
                console.log('Failed to parse JSON :', error);
                bookingMessage.innerText = 'เกิดข้อผิดพลาด: Response ไม่ถูกต้อง';
                alert('Error');
                bookingMessage.style.color = '#c0392b';
                // bookBtn.disabled = false;
            });
    })

    function resetForm() {
        checkinInput.value = '';
        checkoutInput.value = '';
        guests.value = '1';


        total_price.value = '0.00';
        roomid.value = '';
        property.value = '';
        room_number.value = '';

        dayDiff.value = '';
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

    checkinInput.addEventListener('change', calculateDays);
    checkoutInput.addEventListener('change', calculateDays);


    propertySelect.addEventListener('change', function() {

        const propertyId = propertySelect.value; // ดึงค่า value
        // ถ้าเลือกบ้านพักว่าง ให้เคลียร์ห้องพัก
        if (!propertyId) {
            roomSelect.innerHTML = '<option value="">-- เลือกห้องพัก --</option>';
            return;
        }

        // ส่ง AJAX ไป PHP

        fetch(`../controls/get_rooms.php?Property_id=${propertyId}`)
            .then(response => {
                //ตรวจสอบค่าผลลัพธ์ที่เกี่ยวNetwork ถ้ามีปัญหาให้แจ้ง network response was not ok
                if (!response.ok) throw new Error('network response was not ok');
                //คืนค่าเป็น json
                return response.json();
            })
            .then(data => {
                // ล้างข้อมูลเก่า

                if (!Array.isArray(data)) {
                    console.error('Data is not an array:', data);
                    alert('รูปแบบข้อมูลไม่ถูกต้อง');
                    return;
                }
                roomSelect.innerHTML = '<option value="">-- เลือกห้องพัก --</option>';
                // เติม option ห้องพักใหม่
                data.forEach(room => {
                    if (!room.Room_id || !room.Room_number || !room.Room_price) {
                        console.warn('ข้อมูลห้องไม่ครบ', room);
                        alert('ข้อมูลห้องไม่ครบ');
                    } else {
                        const option = document.createElement('option');
                        const price = document.getElementById('price');
                        option.value = room.Room_id;
                        option.textContent =
                            `ห้องที่ ${room.Room_number} ราคา ${room.Room_price} บาท`;
                        option.dataset.price = room.Room_price;
                        option.dataset.roomId = room.Room_id;
                        roomSelect.appendChild(option);

                    }
                });

            })
            .catch(error => {
                alert('Error');
                console.error('Error fetching rooms:', error);
            });

    });
    roomSelect.addEventListener('change', function() {

        const selectOption = roomSelect.options[roomSelect.selectedIndex];
        price.value = selectOption.dataset.price;
        roomId.value = selectOption.dataset.roomId;

    });
    </script>
</body>

</html>