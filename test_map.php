<!DOCTYPE html>
<html>

<head>
    <title>Leaflet Search Example</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

    <style>
    #map {
        height: 600px;
    }
    </style>
</head>

<body>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

    <script>
    // 1. สร้างแผนที่และกำหนด View เริ่มต้น
    const map = L.map('map').setView([13.7563, 100.5018], 10); // [lat, lng], zoom

    // 2. เพิ่ม Tile Layer (พื้นหลังแผนที่)
    // ในที่นี้ใช้ OpenStreetMap ซึ่งเป็นแบบฟรี
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // 3. สร้าง Geocoder Control (ช่องค้นหา) และเพิ่มลงในแผนที่
    const geocoder = L.Control.geocoder({
            defaultMarkGeocode: false // ไม่ต้องให้ Plugin ปักหมุดให้อัตโนมัติ
        })
        .on('markgeocode', function(e) {
            // ฟังก์ชันนี้จะทำงานเมื่อค้นหาเจอและเลือกสถานที่แล้ว

            // 4. จัดการผลลัพธ์การค้นหา
            const bbox = e.geocode.bbox; // ขอบเขตของสถานที่ที่ค้นเจอ
            const latlng = e.geocode.center; // พิกัดกลางของสถานที่

            // สร้าง Marker และปักลงไปที่พิกัดนั้น
            L.marker(latlng)
                .addTo(map)
                .bindPopup(e.geocode.name) // แสดงชื่อสถานที่เมื่อคลิก
                .openPopup();

            // ขยับแผนที่ให้พอดีกับขอบเขตของสถานที่นั้นๆ
            map.fitBounds(bbox);
        })
        .addTo(map);
    </script>
</body>

</html>