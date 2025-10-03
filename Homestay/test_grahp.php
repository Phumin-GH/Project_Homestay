<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Revenue Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 80%;
            max-width: 900px;
            margin: 50px auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            background-color: #fff;
        }
    </style>
</head>
<body>

    <div class="chart-container">
        <canvas id="revenueChart"></canvas>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');
            let myChart; // ประกาศตัวแปร Chart ไว้นอกฟังก์ชัน

            // 1. ฟังก์ชันสำหรับดึงข้อมูลและสร้างกราฟ
            async function fetchRevenueData() {
                try {
                    // 2. ยิง Fetch ไปยัง API ที่เราสร้างไว้
                    const response = await fetch('test_grahp1.php');
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const chartData = await response.json();

                    // 3. นำข้อมูลที่ได้มาสร้างกราฟ
                    if (myChart) {
                        myChart.destroy(); // ทำลายกราฟเก่า (ถ้ามี) ก่อนสร้างใหม่
                    }
                    
                    myChart = new Chart(ctx, {
                        type: 'bar', // ประเภทของกราฟ (bar, line, pie, etc.)
                        data: {
                            labels: chartData.labels, // เอา labels จาก JSON มาใส่
                            datasets: [{
                                label: 'ยอดขายรายเดือน (บาท)',
                                data: chartData.data, // เอา data จาก JSON มาใส่
                                backgroundColor: 'rgba(30, 84, 112, 0.6)',
                                borderColor: 'rgba(30, 84, 112, 1)',
                                borderWidth: 1,
                                borderRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        // จัดรูปแบบตัวเลขแกน Y ให้มี comma
                                        callback: function(value) {
                                            return value.toLocaleString('th-TH');
                                        }
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed.y !== null) {
                                                label += context.parsed.y.toLocaleString('th-TH', { style: 'currency', currency: 'THB' });
                                            }
                                            return label;
                                        }
                                    }
                                }
                            }
                        }
                    });

                } catch (error) {
                    console.error('Error fetching chart data:', error);
                    // อาจจะแสดงข้อความ Error บนหน้าเว็บแทน
                    ctx.canvas.parentNode.innerHTML = '<p style="color:red; text-align:center;">ไม่สามารถโหลดข้อมูลกราฟได้</p>';
                }
            }

            // 4. เรียกใช้ฟังก์ชันเพื่อแสดงกราฟครั้งแรก
            fetchRevenueData();
        });
    </script>

</body>
</html>