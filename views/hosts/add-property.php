<?php
session_start();
if (!isset($_SESSION["Host_email"])) {
    header("Location: host-login.php");
    exit();
}
require_once __DIR__ . "/../../controls/log_hosts.php";
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
            max-width: 1250px;
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

        .form-row-4 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr 80px;
            align-items: flex-end;
            gap: 1rem;
        }

        .map-container {
            margin-bottom: 1.5rem;
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

        .btn-secondary:hover i {
            animation: identifier 0.5s ease-in infinite;
            background: linear-gradient(180deg, #290307ff 20%, #5e0e16ff 20%, #721c24 20%, #f8d7da 20%, #f9eaebff 20%);
        }

        @keyframes identifier {
            0% {
                transform: translateX(5px);
            }

            50% {
                transform: translateX(-5px);
            }

            100% {
                transform: translateX(0px);
            }

        }

        .btn-add {
            background: #017587ff;
            color: white;

        }

        .btn-add:hover {
            background: #029ab2ff;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1)
        }

        .btn-remove {
            background: white;
            color: #b60303ff;
            border: 2px solid #b60303ff;

        }

        .btn-remove:hover {
            /* background: #f40303ff;
        color: white; */
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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

            .form-row-4 {
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

        .form-add-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin: 0.5rem 0 1rem 0;
            padding-top: 2rem;
        }

        .file-input-container {
            padding: 1rem;
            position: relative;
            margin-top: 1.5rem;
            min-height: 16rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border: 3px dashed #ccc;
            border-radius: 8px;
            cursor: pointer;
            transition: border-color 0.3s ease, background 0.3s ease;
        }

        .file-input-container:hover {
            background: #F2F7FE;
            border-color: #3B82F6;
        }

        .file-input-container input[type="file"] {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-container .file-icon {
            font-size: 4rem;
            color: #3B82F6;
            margin-bottom: 0.5rem;
        }

        .file-input-container .file-info,
        .files-info {
            font-size: 1.1rem;
            color: #333;
        }

        .preview-image {
            margin-top: 1rem;
            max-width: 200px;
            max-height: 200px;
            border-radius: 6px;
            object-fit: cover;
            display: none;
            border: 2px solid #ddd;
        }

        .description {
            margin: 1rem 0;
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #666;
        }

        .zone-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            gap: 1rem;
        }

        .zone-help {
            font-size: 0.85rem;
            background: #F3F4F6;
            padding: 0.5rem 1rem;
            border-radius: 6px;
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
                <?php if ($hosts['Host_Status'] == 'pending_verify'): ?>
                    <li><a href="add-property.php" class="active" title="ลงทะเบียนบ้านพักใหม่"><i
                                class="fas fa-user-plus"></i>
                            <span class="menu-label">ลงทะเบียนบ้านพักใหม่</span></a></li>
                <?php endif; ?>
                <li><a href="host-dashboard.php" title="รายงาน"><i class="fas fa-tachometer-alt"></i><span
                            class="menu-label">Dashboard</span></a></li>
                <li><a href="profile.php" title="โปรไฟล์"><i class="fas fa-user"></i><span
                            class="menu-label">Profile</span></a>
                </li>
                <?php if ($hosts['Host_Status'] == 'active'): ?>
                    <li><a href="manage-property.php" class="active" title="จัดการบ้านพัก"><i class="fas fa-plus"></i><span
                                class="menu-label">Manage
                                Property</span></a></li>


                    <li><a href="list_booking.php" title="รายการที่จองเข้ามา"><i class="fa-solid fa-list-ul"></i><span
                                class="menu-label">List Bookings</span></a></li><?php endif; ?>
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
                    <h1><i class="fas fa-plus"></i> เพิ่มบ้านพักใหม่</h1>
                    <p>กรอกข้อมูลบ้านพักของคุณเพื่อเริ่มรับการจอง</p>
                </div>

                <!-- Approval Notice -->
                <div class="approval-notice">
                    <i class="fas fa-info-circle"></i>
                    <strong>หมายเหตุ:</strong>
                    บ้านพักที่เพิ่มใหม่จะต้องได้รับการอนุมัติจากผู้ดูแลระบบก่อนจึงจะสามารถแสดงในระบบและรับการจองได้
                </div>
                <div class="form-card">
                    <form id="formInput">
                        <div class="form-group">
                            <label for="property_name" class="form-label">รูปโปรไฟล์บ้านพัก<span
                                    class="required">*</span></label>
                            <div class="file-input-container" id="dropSingle-zone">
                                <div class="file-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                <input type="file" id="single_image" name="singleImage" accept="image/*" required>
                                <p class="file-info">ลาก & คลิก เพื่ออัปโหลดไฟล์</p>
                            </div>
                            <div id="image-preview" class="preview-image"></div>
                            <div class="description">
                                <span>Max file size : 25MB</span>
                                <span id="file-size"></span>
                                <div class="zone-action">
                                    <div class="zone-help">
                                        <p>ℹ️ รองรับไฟล์ .jpg .png</p>
                                    </div>
                                </div>
                            </div>

                            <label for="property_name" class="form-label">
                                ชื่อบ้านพัก <span class="required">*</span>
                            </label>
                            <input type="text" id="property_name" name="house_name" class="form-input"
                                value="<?php echo htmlspecialchars($property_name ?? ''); ?>" required>
                        </div>

                        <div class="form-row-3">
                            <div class="form-group">
                                <label for="property_province" class="form-label">
                                    จังหวัด <span class="required">*</span>
                                </label>
                                <input type="text" id="property_province" name="province" class="form-input"
                                    value="<?php echo htmlspecialchars($property_province ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="property_district" class="form-label">
                                    อำเภอ <span class="required">*</span>
                                </label>
                                <input type="text" id="property_district" name="district" class="form-input"
                                    value="<?php echo htmlspecialchars($property_district ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="property_subdistrict" class="form-label">
                                    ตำบล <span class="required">*</span>
                                </label>
                                <input type="text" id="property_subdistrict" name="subdistrict" class="form-input"
                                    value="<?php echo htmlspecialchars($property_subdistrict ?? ''); ?>" required>
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
                            <!-- <form method="POST" action="save_location.php">
                                <label>Latitude: <input type="text" id="latitude" name="latitude" readonly></label><br>
                                <label>Longitude: <input type="text" id="longitude" name="longitude"
                                        readonly></label><br><br>
                                <button type="submit">บันทึกพิกัด</button>
                            </form> -->
                            <div class="coordinates-display">
                                <div class="coordinate-item">
                                    <div class="coordinate-label">ละติจูด (Latitude)</div>

                                    <div class="coordinate-value">
                                        <input class="form-input" type="text" id="latitude" name="latitude"
                                            placeholder="ละติจูด (Latitude)" readonly required>
                                    </div>
                                </div>
                                <div class="coordinate-item">
                                    <div class="coordinate-label">ลองติจูด (Longitude)</div>
                                    <div class="coordinate-value">
                                        <input class="form-input" type="text" id="longitude" name="longitude"
                                            placeholder="ลองติจูด (Longitude)" readonly required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="property_image" class="form-label">
                                    รูปบ้านพัก <span class="required">*</span>
                                </label>
                                <div class="file-input-container" id="dropMulti-zone">
                                    <div class="file-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                                    <input type="file" id="multi_image" name="multipleimage[]" accept="image/*" multiple
                                        required>
                                    <p class="files-info">ลาก & คลิก เพื่ออัปโหลดไฟล์</p>
                                </div>
                                <div id="multiImage-preview" class="preview-image"></div>
                                <div class="description">
                                    <span>Max file size : 25MB</span>
                                    <span id="files-size"></span>
                                    <div class="zone-action">
                                        <div class="zone-help">
                                            <p>ℹ️ รองรับไฟล์ .jpg .png</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-add-actions">
                                    <button id="addForm" type="button" class="btn btn-add"><i
                                            class="fa-solid fa-plus"></i>
                                        เพิ่มฟอร์ม</button>
                                    <button id="removeAll" type="button" class="btn btn-remove"><i
                                            class="fa-solid fa-trash"></i>
                                        ลบทั้งหมด</button>
                                </div>
                                <label class="form-label">
                                    เพิ่มห้องพัก <span class="required">*</span>
                                </label>
                                <div id="forms"></div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" id="add_property" name="add_property">
                                    <i class="fas fa-save"></i> บันทึกบ้านพัก
                                </button>
                                <button type="button" class="btn btn-secondary"
                                    onclick="window.location.href='host-dashboard.php'">
                                    <i class="fas fa-arrow-left"></i> ยกเลิก
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&libraries=places"></script> -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-with-sidebar');
            sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("sidebar-collapsed");
        }
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

            // ตั้งค่าละติจูด/ลองจิจูดลงในฟอร์ม
            document.getElementById("latitude").disabled = false;
            document.getElementById("longitude").disabled = false;
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
        });
    </script>
    <script>
        const formsDiv = document.getElementById("forms");
        const addBtn = document.getElementById("addForm");
        const removeAllBtn = document.getElementById("removeAll");
        let formCount = 0;
        const maxForms = 20;

        // ➕ เพิ่มฟอร์มใหม่
        addBtn.addEventListener("click", () => {
            if (formCount < maxForms) {
                formCount++;
                const form = document.createElement("div");
                form.classList.add("form-container");
                form.setAttribute("id", "form-" + formCount);

                form.innerHTML = `
             <div class="form-row-4">
          <label class="form-label">เลขห้อง:<span class="required">*</span><input type="text" name="roomNum[]" placeholder="1" class="form-input"></label>
          <label class="form-label">ราคา:<span class="required">*</span><input type="number" name="roomPrice[]" placeholder="550" class="form-input"></label>
          <label class="form-label">ประเภทห้อง:<span class="required">*</span><input type="text" name="roomCap[]" placeholder="ห้องเดี่ยว" class="form-input"></label>
          <label class="form-label">สิ่งอำนวยความสะดวก:<span class="required">*</span><input type="text" name="roomUten[]" placeholder="พัดลม,กาต้มน้ำ" class="form-input"></label>
          <button type="button" class="btn btn-remove"><i class="fa-solid fa-delete-left"></i></button>
          </div>`;
                formsDiv.appendChild(form);
                // ลบฟอร์มเดียว
                form.querySelector(".btn-remove").addEventListener("click", () => {
                    form.remove();
                    formCount--;
                });
            } else {
                alert("สร้างฟอร์มได้สูงสุด 20 ฟอร์มเท่านั้น!");
            }
        });
        // ฟอร์มลบทั้งหมด
        removeAllBtn.addEventListener("click", () => {
            formsDiv.innerHTML = "";
            formCount = 0;
        });
        document.addEventListener("DOMContentLoaded", () => {
            const multi_image = document.getElementById("multi_image");
            const single_image = document.getElementById("single_image");
            const preview = document.getElementById("image-preview");
            const info = document.querySelector(".file-info");
            const infos = document.querySelector(".files-info");
            const sizeInfos = document.getElementById("files-size");
            const sizeInfo = document.getElementById("file-size");
            const dropMultiZone = document.getElementById("dropMulti-zone");
            const dropSingleZone = document.getElementById("dropSingle-zone");
            multi_image.addEventListener("change", function() {
                const files = this.files;
                const Multipreview = document.getElementById("multiImage-preview");
                // เคลียร์ preview เก่า
                Multipreview.innerHTML = "";
                sizeInfos.textContent = "";
                if (files.length > 0) {
                    Array.from(files).forEach(file => {
                        if (!file.type.startsWith("image/")) {
                            infos.textContent = "❌ กรุณาเลือกไฟล์รูปภาพเท่านั้น";
                            return;
                        }
                        // ขนาดไฟล์
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                        sizeInfos.textContent += `${file.name} - ${fileSizeMB} MB\n`;
                        // แสดงรูป
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement("img");
                            img.src = e.target.result;
                            img.classList.add("preview-image");
                            img.style.maxWidth = "150px";
                            img.style.margin = "5px";
                            Multipreview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    });
                    infos.textContent = `${files.length} ไฟล์ถูกเลือกแล้ว`;
                } else {
                    infos.textContent = "ลาก & คลิก เพื่ออัปโหลดไฟล์";
                }
            })
            // Drag & Drop Zone
            dropMultiZone.addEventListener("dragover", e => {
                e.preventDefault();
                dropMultiZone.style.background = "#E0F2FE";
            })
            dropMultiZone.addEventListener("dragleave", () => {
                dropMultiZone.style.background = "transparent";
            })
            dropMultiZone.addEventListener("drop", e => {
                e.preventDefault();
                multi_image.files = e.dataTransfer.files;
                multi_image.dispatchEvent(new Event("change"));
                dropMultiZone.style.background = "transparent";
            });
            single_image.addEventListener("change", function() {
                const file = this.files[0];
                if (file) {
                    if (!file.type.startsWith("image/")) {
                        info.textContent = "❌ กรุณาเลือกไฟล์รูปภาพ";
                        preview.style.display = "none";
                        return;
                    }
                    // ขนาดไฟล์
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);
                    sizeInfo.textContent = `File size: ${fileSizeMB} MB`;
                    info.textContent = file.name;
                    const reader = new FileReader();
                    reader.onload = e => {
                        preview.src = e.target.result;
                        preview.style.display = "block";
                    };
                    reader.readAsDataURL(file);
                } else {
                    info.textContent = "ลาก & คลิก เพื่ออัปโหลดไฟล์";
                    preview.style.display = "none";
                    sizeInfo.textContent = "";
                }
            })

            dropSingleZone.addEventListener("dragover", e => {
                e.preventDefault();
                dropSingleZone.style.background = "#E0F2FE";
            })
            dropSingleZone.addEventListener("dragleave", () => {
                dropSingleZone.style.background = "transparent";
            })
            dropSingleZone.addEventListener("drop", e => {
                e.preventDefault();
                single_image.files = e.dataTransfer.files;
                single_image.dispatchEvent(new Event("change"));
                dropSingleZone.style.background = "transparent";
            });

        });
        const btnInput = document.getElementById('add_property');
        const formInput = document.getElementById('formInput');
        btnInput.addEventListener('click', function(event) {
            event.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
            const formData = new FormData(formInput);
            formData.append("add_property", "1");
            const multiFiles = document.getElementById("multi_image").files;
            for (let i = 0; i < multiFiles.length; i++) {
                formData.append("multi_image[]", multiFiles[i]);
            }
            fetch('../../controls/add_edit_property.php', {
                    method: 'POST',
                    body: formData,

                })
                .then(response => response.json())
                .then(data => {
                    // แสดงข้อความตอบกลับจากเซิร์ฟเวอร์
                    if (data.success == true) {
                        alert(data.message);
                        console.log(data.message);
                        window.location.href = "host-dashboard.php";
                    } else {
                        alert(data.message);
                        console.log(data);
                        // ถ้าต้องการรีเฟรชหน้าเว็บหลังจากส่งข้อมูลสำเร็จ
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
                    window.location.reload();
                });
        });
    </script>

</body>

</html>

</html>