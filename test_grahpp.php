<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Charts Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background-color: #f4f7f6;
            font-family: sans-serif;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }

        .chart-container {
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }

        .chart-container h2 {
            margin-top: 0;
            color: #333;
        }
    </style>
</head>

<body>

    <div class="dashboard-grid">
        <!-- 1. กราฟแท่ง (Bar Chart) -->
        <div class="chart-container">
            <h2>ยอดขายรายเดือน</h2>
            <canvas id="barChart"></canvas>
        </div>
        <!-- 2. กราฟเส้น (Line Chart) -->
        <div class="chart-container">
            <h2>ผู้สมัครใหม่ (30 วันล่าสุด)</h2>
            <canvas id="lineChart"></canvas>
        </div>
        <!-- 3. กราฟวงกลม (Pie Chart) -->
        <div class="chart-container">
            <h2>สัดส่วนช่องทางการชำระเงิน (Pie)</h2>
            <canvas id="pieChart"></canvas>
        </div>
        <!-- 4. กราฟโดนัท (Doughnut Chart) -->
        <div class="chart-container">
            <h2>สัดส่วนช่องทางการชำระเงิน (Doughnut)</h2>
            <canvas id="doughnutChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                // --- 1. กราฟแท่ง (Bar Chart) ---
                const revenueResponse = await fetch('test_grahpp1.php?type=revenue');
                // const revenueData = await revenueResponse.json();
                const revenueData = await revenueResponse.text();
                console.error(revenueData);
                new Chart(document.getElementById('barChart'), {
                    type: 'bar',
                    data: {
                        labels: revenueData.labels,
                        datasets: [{
                            label: 'ยอดขาย (บาท)',
                            data: revenueData.data,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)'
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // --- 2. กราฟเส้น (Line Chart) ---
                const userResponse = await fetch('test_grahpp1.php?type=users');
                // const userData = await userResponse.json();
                const userData = await userResponse.text();
                console.error(userData);
                new Chart(document.getElementById('lineChart'), {
                    type: 'line',
                    data: {
                        labels: userData.labels,
                        datasets: [{
                            label: 'ผู้สมัครใหม่',
                            data: userData.data,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            fill: false,
                            tension: 0.1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // --- 3 & 4. กราฟวงกลมและโดนัท (ใช้ข้อมูลชุดเดียวกัน) ---
                const paymentResponse = await fetch('test_grahpp1.php?type=payments');
                const paymentData = await paymentResponse.json();
                const pieAndDoughnutConfig = {
                    data: {
                        labels: paymentData.labels,
                        datasets: [{
                            label: 'จำนวน',
                            data: paymentData.data,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(75, 192, 192, 0.7)'
                            ],
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true
                    }
                };

                // สร้าง Pie Chart
                new Chart(document.getElementById('pieChart'), {
                    type: 'pie',
                    ...pieAndDoughnutConfig // ใช้ Config เดียวกัน
                });

                // สร้าง Doughnut Chart
                new Chart(document.getElementById('doughnutChart'), {
                    type: 'doughnut',
                    ...pieAndDoughnutConfig // ใช้ Config เดียวกัน
                });

            } catch (error) {
                console.error('Failed to render charts:', error);
                document.body.innerHTML =
                    '<p style="color:red; text-align:center;">ไม่สามารถโหลดข้อมูล Dashboard ได้</p>';
            }
        });
    </script>
</body>

</html>