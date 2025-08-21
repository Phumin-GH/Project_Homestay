<?php
session_start();
include __DIR__ . "/../config/db_connect.php";
if (!isset($_SESSION["Host_email"])) {
header("Location: host-login.php");
exit();
}
if(isset($_POST['Property_id'])){
$property_id = $_POST['Property_id'];
$sql = "SELECT p.*,r.* FROM property p
INNER JOIN room r ON p.Property_id = r.Property_id
WHERE p.Property_id = ?
";
$stmt = $conn->prepare($sql);
$stmt -> execute([$property_id]);
$houses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เพิ่มบ้านพัก - Homestay Booking</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/main-menu.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    .form-container {
        max-width: 1000px;
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
        cursor: pointer;
        transition: background-color 0.5s ease-in-out 0.01s, color 0.7s ease-in-out 0.01s, transform 0.4s ease-in 0.01s;

    }

    .page-header:hover {

        color: #1e5470;
        border: 2px solid #1e5470;
        background-color: #66beebff;
        transform: translateY(-10px);
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .form-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e5e5;
        padding: 2rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px solid #e5e5e5;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.2s ease;
        box-sizing: border-box;
    }

    .form-input:focus {
        outline: none;
        border-color: #1e5470;
    }

    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1rem;
    }

    .map-container {
        margin-bottom: 1.5rem;
        z-index: 0;
    }

    .map-instructions {
        background: #e3f2fd;
        border: 1px solid #1e5470;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        color: #1e5470;
    }

    .map-instructions i {
        margin-right: 0.5rem;
    }

    #map {
        width: 100%;
        height: 400px;
        border-radius: 8px;
        border: 2px solid #e5e5e5;
    }

    .coordinates-display {
        background: #f8f9fa;
        border: 1px solid #e5e5e5;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .coordinate-item {
        text-align: center;
    }

    .coordinate-label {
        font-size: 0.875rem;
        color: #666;
        margin-bottom: 0.25rem;
    }

    .coordinate-value {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1a1a1a;
    }

    .no-coordinates {
        color: #999;
        font-style: italic;
    }

    .file-input-container {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .file-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-input-label {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        border: 2px dashed #e5e5e5;
        border-radius: 8px;
        background: #f8f9fa;
        cursor: pointer;
        transition: all 0.2s ease;
        min-height: 120px;
    }

    .file-input-label:hover {
        border-color: #1e5470;
        background: #f0f2ff;
    }

    .file-input-label i {
        font-size: 2rem;
        color: #666;
        margin-right: 0.5rem;
    }

    .preview-image {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        margin-top: 1rem;
        display: none;
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
        background: #256a8cff;
        color: white;
    }

    .btn-secondary {
        background: white;
        color: #5a6268;
        border: 2px solid #5a6268;
    }

    .btn-secondary:hover {
        background: #6c757d;
        color: white;

    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e5e5;
    }

    .alert {
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-info {
        background: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    .required {
        color: #dc3545;
    }

    .approval-notice {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        color: #856404;
    }

    .approval-notice i {
        margin-right: 0.5rem;
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 1rem;
        }

        .form-row-3 {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        #map {
            height: 300px;
        }

        .coordinates-display {
            flex-direction: column;
            gap: 1rem;
        }
    }

    .box-img {
        width: 25rem;
        height: 15rem;
        object-fit: cover;
        border-radius: 8px;
        margin-right: 1rem;
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
                <li><a href="manage-property.php" title="จัดการบ้านพัก" class="active"><i class="fas fa-plus"></i><span
                            class="menu-label">Manage
                            Property</span></a></li>

                <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                            class="menu-label">Test</span></a></li>
                <li><a href="walkin-property.php" title="การจอง"><i class="fa-solid fa-person-walking"></i><span
                            class="menu-label">Walkin</span></a></li>
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
                <div class="page-header">
                    <h1><i class="fas fa-plus"></i> เพิ่มบ้านพักใหม่</h1>
                    <p>คลิกที่ตรงนี้ เพื่อลงทะเบียนบ้านพักของคุณเพิ่ม</p>
                </div>



                <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <div class="form-card">
                    <form method="POST" enctype="multipart/form-data" action="../controls/add_edit_property.php">
                        <?php

                        if (isset($_SESSION['error'])) {
                            echo "<div class='alert alert-error'><i class='fa-solid fa-ban'></i>" . $_SESSION['error'] . "</div>";
                            unset($_SESSION['error']);
                        }
                        ?>
                        <?php foreach ($houses as $house):?>
                        <img class="box-img" src="../<?php echo htmlspecialchars( $house['Property_image']); ?>" alt="">
                        <div class="form-group">
                            <label for="property_name" class="form-label">
                                ชื่อบ้านพัก <span class="required">*</span>
                            </label>
                            <input type="text" id="property_name" name="house_name" class="form-input"
                                value="<?php echo htmlspecialchars( $house['Property_name']); ?>" required>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="property_province" class="form-label">
                                    จังหวัด <span class="required">*</span>
                                </label>
                                <input type="text" id="property_province" name="province" class="form-input"
                                    value="<?php echo htmlspecialchars($house['Property_province']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="property_district" class="form-label">
                                    อำเภอ <span class="required">*</span>
                                </label>
                                <input type="text" id="property_district" name="district" class="form-input"
                                    value="<?php echo htmlspecialchars($house['Property_district']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="property_subdistrict" class="form-label">
                                    ตำบล <span class="required">*</span>
                                </label>
                                <input type="text" id="property_subdistrict" name="subdistrict" class="form-input"
                                    value="<?php echo htmlspecialchars($house['Property_subdistrict']); ?>" required>
                            </div>

                        </div>

                        <!-- Google Maps Section -->
                        <div class="form-group">
                            <label class="form-label">
                                เลือกตำแหน่งบนแผนที่ <span class="required">*</span>
                            </label>
                            <!-- <div class="map-instructions">
                                <i class="fas fa-info-circle"></i>
                                คลิกบนแผนที่เพื่อเลือกตำแหน่งของบ้านพัก หรือใช้ช่องค้นหาด้านล่าง
                            </div> -->

                            <!-- Search Box -->
                            <!--<div class="form-group">
                                <input type="text" id="search-box" class="form-input"
                                    placeholder="ค้นหาสถานที่ เช่น กรุงเทพ, เชียงใหม่, ภูเก็ต">
                            </div>-->

                            <!-- Map Container -->
                            <div class="map-container">
                                <div id="map"></div>

                            </div>
                            <!--<form method="POST" action="save_location.php">
                                <label>Latitude: <input type="text" id="latitude" name="latitude" readonly></label><br>
                                <label>Longitude: <input type="text" id="longitude" name="longitude"
                                        readonly></label><br><br>
                                <button type="submit">บันทึกพิกัด</button>
                            </form>-->

                            <!-- Coordinates Display -->
                            <div class="coordinates-display">
                                <div class="coordinate-item">
                                    <div class="coordinate-label">ละติจูด (Latitude)</div>

                                    <div class="coordinate-value">
                                        <input class="form-input" type="text" id="latitude" name="latitude"
                                            value="<?php echo htmlspecialchars($house['Property_latitude']); ?>"
                                            placeholder="ละติจูด (Latitude)" readonly required>

                                        <!--<span class="no-coordinates">ยังไม่ได้เลือกตำแหน่ง</span>-->
                                    </div>
                                </div>
                                <div class="coordinate-item">
                                    <div class="coordinate-label">ลองติจูด (Longitude)</div>

                                    <div class="coordinate-value">
                                        <input class="form-input" type="text" id="longitude" name="longitude"
                                            value="<?php echo htmlspecialchars($house['Property_longitude']); ?>"
                                            placeholder="ลองติจูด (Longitude)" readonly required>
                                        <!--<span class="no-coordinates">ยังไม่ได้เลือกตำแหน่ง</span>-->
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="property_image" class="form-label">
                                    รูปบ้านพัก <span class="required">*</span>
                                </label>
                                <div class="file-input-container">
                                    <input type="file" id="property_image" name="image" class="file-input"
                                        accept="image/*" onchange="previewImage(this)" required>
                                    <label for="property_image" class="file-input-label">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <span>คลิกเพื่อเลือกรูปภาพ หรือลากไฟล์มาวางที่นี่</span>
                                    </label>
                                </div>
                                <img id="image-preview" class="preview-image" alt="Preview">
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" name="add_property">
                                    <i class="fas fa-save"></i> บันทึกบ้านพัก
                                </button>
                                <a href="host-dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> ยกเลิก
                                </a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </form>
                </div>

                <!-- Hidden inputs for form submission -->
                <!--<input type="hidden" id="property_latitude" name="property_latitude"
                    value="<?php /* echo htmlspecialchars($property_latitude ?? ''); */ ?>" required>
                <input type="hidden" id="property_longitude" name="property_longitude"
                    value="<?php /* echo htmlspecialchars($property_longitude ?? ''); */ ?>" required>-->
            </div>



        </div>
    </div>

    </div>

    <!-- Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script>

    <script>
    const head = document.getElementsByClassName('page-header')[0];
    head.addEventListener('click', function() {
        window.location.href = "add-property.php";
    })
    </script>


    <script>
    // สร้างแผนที่เริ่มต้นที่กรุงเทพ
    const map = L.map('map').setView([13.7563, 100.5018], 13);

    // ใช้แผนที่จาก OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);


    let marker;

    // เมื่อคลิกที่แผนที่
    map.on('click', function(e) {
        const lat = e.latlng.lat.toFixed(6);
        const lng = e.latlng.lng.toFixed(6);

        // ถ้ามี marker อยู่แล้วให้ย้ายตำแหน่ง
        if (marker) {
            marker.setLatLng(e.latlng);
        } else {
            marker = L.marker(e.latlng, {
                draggable: true
            }).addTo(map);
            marker.on('dragend', function() {
                const pos = marker.getLatLng();
                document.getElementById("latitude").value = pos.lat.toFixed(6);
                document.getElementById("longitude").value = pos.lng.toFixed(6);
            });
        }
        // if (!lat && !lng) {
        //     alert("กรุณาคลิกที่แผนที่เพื่อตั้งค่าตำแหน่ง");
        //     return false;
        // }

        // ตั้งค่าละติจูด/ลองจิจูดลงในฟอร์ม
        document.getElementById("latitude").disabled = false;
        document.getElementById("longitude").disabled = false;
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
    });
    </script>
    <script>
    function previewImage(input) {
        const preview = document.getElementById('image-preview');
        const file = input.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block'; // เผื่อซ่อนไว้ตอนแรก
        };

        if (file) {
            reader.readAsDataURL(file);
        }
    }
    </script>

</body>

</html>

</html>