<!DOCTYPE html>
<html lang="th">

<head>
    <title>ค้นหาสินค้า</title>
    <style>
        /* ตัวอย่าง CSS สำหรับ Flexbox */
        #product-list-container {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .product-card {
            border: 1px solid #ccc;
            padding: 1rem;
            border-radius: 8px;
            width: 200px;
        }
    </style>
</head>

<body>
    <h1>ค้นหาสินค้า</h1>
    <div class="search-container">
        <label for="search-input">ค้นหาชื่อสินค้า:</label>
        <input type="search" id="search-input" placeholder="พิมพ์ชื่อสินค้าที่นี่...">
        <button id="search">ค้นหา</button>
    </div>
    <hr>
    <div id="product-list-container">
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-input');
            const productContainer = document.getElementById('product-list-container');
            const searchBtn = document.getElementById('search');
            // ฟังก์ชันสำหรับดึงและแสดงผลสินค้า
            function fetchProducts(searchTerm = '') {
                // ใช้ Fetch API ส่งคำค้นหาไปที่ PHP
                fetch('test_search1.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({
                            'search_query': searchTerm
                        })
                    })
                    .then(response => response.json())
                    // .then(response => response.text())
                    .then(products => {
                        // เคลียร์ข้อมูลเก่าใน container
                        console.log(products);
                        productContainer.innerHTML = '';

                        if (products.length > 0) {
                            // วนลูปสร้างการ์ดสินค้าจากข้อมูลที่ได้
                            products.forEach(product => {
                                const productCard = document.createElement('div');
                                productCard.className = 'product-card';
                                productCard.innerHTML = `
                            <h4>${product.Property_name}</h4>
                            <p>ราคา: ${product.Host_firstname} ${product.Host_lastname } บาท</p>
                        `;
                                productContainer.appendChild(productCard);
                            });
                        } else {
                            // กรณีไม่พบสินค้า
                            productContainer.innerHTML = '<p>ไม่พบสินค้าที่ตรงกับคำค้นหา</p>';
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }

            // เมื่อผู้ใช้พิมพ์ในช่องค้นหา ให้เรียกใช้ฟังก์ชัน fetchProducts
            searchBtn.addEventListener('click', function() {
                console.log(searchInput.value);
                fetchProducts(searchInput.value);
            });

            // โหลดสินค้าทั้งหมดเมื่อเปิดหน้าเว็บครั้งแรก
            fetchProducts();
        });
    </script>

</body>

</html>