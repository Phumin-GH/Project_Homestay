<?php
session_start();
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}
require_once __DIR__ . "/../../controls/log_hosts.php";
require_once __DIR__ . "/../../api/get_checkin.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มบ้านพัก - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .page-header {
            background: linear-gradient(155deg, #1e5470 0%, #74adc9ff 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: 16px;
            /* margin: 1.5rem 8rem 0 8rem; */
            margin-bottom: 3rem;
            text-align: center;
            border-bottom: 1px solid #e5e5e5;
            /* width: 55rem; */
            overflow: hidden;


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
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 2rem;
        }

        table-container {
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {


            .table-container {
                padding: 1rem;
            }
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e5e5;
            font-size: 0.9rem;
        }

        th {
            background: #f8f9fa;
            font-weight: 500;
            color: #1a1a1a;
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        td {
            color: #333;
        }

        .btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            text-decoration: none;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
        }

        .btn-primary {
            background: #1e5470;
            color: white;
        }

        .btn-primary:hover {
            background: #256a8c;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-disable {
            background: #909090ff;
            color: white;
            pointer-events: none;
        }

        .status-pending {
            color: #856404;
            background: #fff3cd;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .status-confirmed {
            color: #155724;
            background: #d4edda;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .status-checked-in {
            color: #004085;
            background: #cce5ff;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .status-checked-out {
            color: #383d41;
            background: #e2e3e5;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .status-cancelled {
            color: #721c24;
            background: #f8d7da;
            padding: 0.2rem 0.6rem;
            border-radius: 10px;
            font-size: 0.8rem;
            display: inline-block;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        /* .alert {
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .alert-info {
        background: #e7f4ff;
        color: #0c5460;
        border: 1px solid #bee5eb;
    } */

        @media (max-width: 768px) {
            .form-container {
                padding: 1rem;
            }

            table {
                display: block;
                overflow-x: auto;
            }

            th,
            td {
                padding: 0.5rem;
                font-size: 0.85rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.3rem;
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

        .UserH2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 1rem 0 1rem 0;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: var(--card-background);
            border-radius: 8px;
            border: 1px dashed var(--border-color);
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--border-color);
            margin-bottom: 1.5rem;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
    </style>
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
                <li><a href="host-dashboard.php" title="รายงาน"><i class="fas fa-tachometer-alt"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <?php if ($hosts['Host_Status'] == 'active'): ?>
                    <li><a href="manage-property.php" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                                class="menu-label">Manage
                                Property</span></a></li>
                    <li><a href="list_booking.php" class="active" title="รายการที่จองเข้ามา"><i
                                class="fa-solid fa-list-ul"></i><span class="menu-label">List Bookings</span></a></li>
                <?php endif; ?>
                <li><a href="walkin-property.php" title="การจอง"><i class="fa-solid fa-person-walking"></i><span
                            class="menu-label">Walkin</span></a></li>
                <li><a href="../../controls/logout.php" title="ออกจากระบบ"><i class="fas fa-sign-out-alt"></i><span
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
                <div class="page-header">
                    <h1><i class="fa-solid fa-rectangle-list"></i>
                        <?php echo htmlspecialchars($pending[0]['Property_name']); ?></h1>
                    <p>รายการจอง</p>

                </div>
                <div class="auth-tabs">
                    <div class="tab active" id="pending-tab">
                        <i class="fas fa-sign-in-alt"></i>User Checkin
                    </div>
                    <div class="tab" id="checkin-tab">
                        <i class="fa-solid fa-users-line"></i> User Checkout
                    </div>
                    <div class="tab" id="checkout-tab">
                        <i class="fa-solid fa-users-slash"></i> Checked Out
                    </div>
                </div>
                <div class="table-container active" id="pending-section">
                    <?php echo "<h1 class='UserH2'>รายการทั้งหมด (" . count($pending) . ")</h1>" ?>
                    <?php if (empty($pending)): ?>
                        <div class="empty-state">
                            <i class="ph ph-heart-break"></i>
                            <h3>ไม่รายการจองเข้ามา</h3>
                            <p>ยังไม่มีรายการจอง</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>

                                    <th>ชื่อผู้จอง</th>
                                    <th>วันที่เช็คอิน</th>
                                    <th>วันที่เช็คเอาท์</th>
                                    <th>จำนวนคน</th>
                                    <th>ราคารวม (บาท)</th>
                                    <th>แหล่งที่มา</th>
                                    <th>สถานะ</th>
                                    <th>สถานะ</th>
                                    <th>การกระทำ</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($pending as $check): ?>
                                    <tr class="form-action" data-book-id="<?php echo $check['Booking_id']; ?>"
                                        data-source="<?php echo htmlspecialchars($check['Source']); ?>">

                                        <td><?php echo htmlspecialchars($check['Firstname'] . ' ' . $check['Lastname']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($check['CheckIn'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($check['Check_out'])); ?></td>
                                        <td><?php echo htmlspecialchars($check['Guests']); ?></td>
                                        <td><?php echo number_format($check['Total_price'], 2); ?> </td>
                                        <td><?php echo htmlspecialchars($check['Source']); ?></td>
                                        <td>
                                            <span class="status-<?php echo strtolower($check['Payment_status']); ?>">
                                                <?php echo htmlspecialchars($check['Payment_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($check['Check_status']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $check['Booking_id']; ?>">
                                                <input type="hidden" name="source"
                                                    value="<?php echo htmlspecialchars($check['Source']); ?>">
                                                <button type="submit" name="chIn_action" value="check_in" title="Check In"
                                                    class="btn btn-success">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>
                                                <button type="submit" name="chOut_action" value="check_out" title="Check Out"
                                                    class="btn btn-disable">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </button>
                                                <button type="submit" name="refund_action" value="refund" title="Refund"
                                                    class="btn btn-danger">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="table-container " id="checkin-section" style="display:none;">
                    <?php echo "<h1>รายการทั้งหมด (" . count($check_in) . ")</h1>" ?>
                    <?php if (empty($check_in)): ?>
                        <!-- <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> ยังไม่มีรายการจอง
                    </div> -->
                        <div class="empty-state">
                            <i class="ph ph-heart-break"></i>
                            <h3>ไม่รายการจองเข้ามา</h3>
                            <p>ยังไม่มีรายการจอง</p>

                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>

                                    <th>ชื่อผู้จอง</th>
                                    <th>วันที่เช็คอิน</th>
                                    <th>วันที่เช็คเอาท์</th>
                                    <th>จำนวนคน</th>
                                    <th>ราคารวม (บาท)</th>
                                    <th>แหล่งที่มา</th>
                                    <th>สถานะ</th>
                                    <th>สถานะ</th>
                                    <th>การกระทำ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($check_in as $check): ?>
                                    <tr class="form-action" data-book-id="<?php echo $check['Booking_id']; ?>"
                                        data-source="<?php echo htmlspecialchars($check['Source']); ?>">
                                        <td><?php echo htmlspecialchars($check['Firstname'] . ' ' . $check['Lastname']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($check['CheckIn'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($check['Check_out'])); ?></td>
                                        <td><?php echo htmlspecialchars($check['Guests']); ?></td>
                                        <td><?php echo number_format($check['Total_price'], 2); ?> </td>
                                        <td><?php echo htmlspecialchars($check['Source']); ?></td>
                                        <td>
                                            <span class="status-<?php echo strtolower($check['Payment_status']); ?>">
                                                <?php echo htmlspecialchars($check['Payment_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($check['Check_status']); ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $check['Booking_id']; ?>">
                                                <input type="hidden" name="source"
                                                    value="<?php echo htmlspecialchars($check['Source']); ?>">
                                                <button type="submit" name="chIn_action" value="check_in" title="Check In"
                                                    class="btn btn-disable">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>
                                                <button type="submit" name="chOut_action" value="check_out" title="Check Out"
                                                    class="btn btn-success">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </button>
                                                <button type="submit" name="refund_action" value="refund" title="Refund"
                                                    class="btn btn-danger">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                <div class="table-container " id="checkout-section" style="display:none;">
                    <?php echo "<h1>รายการทั้งหมด (" . count($check_out) . ")</h1>" ?>

                    <?php if (empty($check_out)): ?>
                        <!-- <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> ยังไม่มีรายการจอง
                    </div> -->
                        <div class="empty-state">
                            <i class="ph ph-heart-break"></i>
                            <h3>ไม่รายการจองเข้ามา</h3>
                            <p>ยังไม่มีรายการจอง</p>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ชื่อผู้จอง</th>
                                    <th>วันที่เช็คอิน</th>
                                    <th>วันที่เช็คเอาท์</th>
                                    <th>จำนวนคน</th>
                                    <th>ราคารวม (บาท)</th>
                                    <th>แหล่งที่มา</th>
                                    <th>สถานะ</th>
                                    <th>สถานะ</th>
                                    <th>การกระทำ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($check_out as $check): ?>
                                    <tr class="form-action" data-book-id="<?php echo $check['Booking_id']; ?>"
                                        data-source="<?php echo htmlspecialchars($check['Source']); ?>">
                                        <td><?php echo htmlspecialchars($check['Firstname'] . ' ' . $check['Lastname']); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($check['CheckIn'])); ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($check['Check_out'])); ?></td>
                                        <td><?php echo htmlspecialchars($check['Guests']); ?></td>
                                        <td><?php echo number_format($check['Total_price'], 2); ?> </td>
                                        <td><?php echo htmlspecialchars($check['Source']); ?></td>
                                        <td>
                                            <span class="status-<?php echo strtolower($check['Payment_status']); ?>">
                                                <?php echo htmlspecialchars($check['Payment_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($check['Check_status']); ?></td>
                                        <td>
                                            <div class="action-buttons">

                                                <input type="hidden" name="booking_id"
                                                    value="<?php echo $check['Booking_id']; ?>">
                                                <input type="hidden" name="source"
                                                    value="<?php echo htmlspecialchars($check['Source']); ?>">
                                                <button type="submit" name="chIn_action" value="check_in" title="Check In"
                                                    class="btn btn-disable">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </button>

                                                <button type="submit" name="chOut_action" value="check_out" title="Check Out"
                                                    class="btn btn-disable">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </button>

                                                <button type="submit" name="refund_action" value="refund" title="Refund"
                                                    class="btn btn-disable">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const row = document.querySelectorAll('.form-action');
            row.forEach(element => {
                const book_id = element.dataset.bookId;
                const source = element.dataset.source;
                const check_in = element.querySelector('[name="chIn_action"]');
                const check_out = element.querySelector('[name="chOut_action"]');
                const refund = element.querySelector('[name="refund_action"]');
                check_in.addEventListener('click', () => sendAction(book_id, source,
                    'Checked_in'));
                check_out.addEventListener('click', () => sendAction(book_id, source,
                    'Checked_out'));
                refund.addEventListener('click', () => sendAction(book_id, source,
                    'Cancelled'));
                // function ส่ง fetch ไป PHP
                function sendAction(book_id, source, actionType) {
                    fetch("../../controls/checkin_actions.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/x-www-form-urlencoded"
                            },
                            body: `book_id=${encodeURIComponent(book_id)}&source=${encodeURIComponent(source)}&action=${encodeURIComponent(actionType)}`
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert(data.message);
                                const elementRow = document.querySelector(
                                    `[data-book-id="${book_id}"]`);
                                const statusCell = elementRow.querySelector(
                                    'td:nth-child(9)'); // หรือ td.status
                                if (statusCell) {
                                    if (actionType === 'Check_in') {
                                        statusCell.textContent =
                                            'Checked In';
                                    }
                                    if (actionType === 'Check_out') {
                                        statusCell.textContent =
                                            'Checked Out';
                                    }
                                    if (actionType === 'Cancelled') {
                                        statusCell.textContent =
                                            'Cancelled';
                                    }
                                    location.reload();
                                }
                            } else {
                                alert('Fail: ' + data.message);
                            }
                        })
                        .catch(err => alert('Errors: ' + err));
                }
            });
            const pending = document.getElementById('pending-tab');
            const checkin = document.getElementById('checkin-tab');
            const checkout = document.getElementById('checkout-tab');
            const Pend_sec = document.getElementById('pending-section');
            const In_sec = document.getElementById('checkin-section');
            const Out_sec = document.getElementById('checkout-section');
            // Tab switching functionality
            pending.addEventListener('click', () => {
                Pend_sec.style.display = 'block';
                In_sec.style.display = 'none';
                Out_sec.style.display = 'none';
                pending.classList.add('active');
                checkin.classList.remove('active');
                checkout.classList.remove('active');
            });
            checkin.addEventListener('click', () => {
                Pend_sec.style.display = 'none';
                In_sec.style.display = 'block';
                Out_sec.style.display = 'none';
                pending.classList.remove('active');
                checkin.classList.add('active');
                checkout.classList.remove('active');
            });
            checkout.addEventListener('click', () => {
                Pend_sec.style.display = 'none';
                In_sec.style.display = 'none';
                Out_sec.style.display = 'block';
                pending.classList.remove('active');
                checkin.classList.remove('active');
                checkout.classList.add('active');
            });
        });
    </script>
    <!-- Google Maps API -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script> -->
    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            if (file) {
                reader.readAsDataURL(file);
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-with-sidebar');
            sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("sidebar-collapsed");
        }
    </script>
</body>

</html>