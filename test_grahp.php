<?php
session_start();

require_once __DIR__ . '/api/get_TotalData.php';

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Homestay Booking</title>
    <link rel="website icon" type="png" href="../../public/images/logo.png">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/main-menu.css">
    <link rel="stylesheet" href="../../public/css/barStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
    .admin-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem;
    }

    .page-header {
        background: #1e5470;
        color: white;
        padding: 3rem 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 2rem;
    }

    .info-card,
    .chart-card {
        background-color: #ffffff;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e5470;
        margin-bottom: 1.5rem;
        border-bottom: 1px solid #e0e0e0;
        padding-bottom: 1rem;
    }

    /* ▼▼▼ 2. CSS สำหรับปุ่มและกราฟ ▼▼▼ */
    .time-range-selector {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .time-range-selector button {
        padding: 8px 16px;
        margin: 0 5px;
        border: 1px solid #ccc;
        background-color: #f0f0f0;
        cursor: pointer;
        border-radius: 20px;
        transition: all 0.2s ease-in-out;
    }

    .time-range-selector button:hover {
        background-color: #ddd;
    }

    .time-range-selector button.active {
        background-color: #1e5470;
        color: white;
        border-color: #1e5470;
    }
    </style>
</head>

<body>
    <div class="main-with-sidebar">
        <div class="admin-container">
            <div class="page-header">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <p>ภาพรวมของระบบการจัดการโฮมสเตย์</p>
            </div>

            <div class="grid-container">
                <div class="info-card">...</div>
                <div class="info-card">...</div>
                <div class="info-card">...</div>

                <div class="chart-card" style="grid-column: 1 / -1;">
                    <h3 class="card-header">
                        <i class="fas fa-chart-line"></i> ภาพรวมรายได้
                    </h3>
                    <div class="time-range-selector">
                        <button data-period="week" class="active">สัปดาห์นี้</button>
                        <button data-period="month">เดือนนี้</button>
                        <button data-period="year">ปีนี้</button>
                    </div>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="info-card">...</div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const timeRangeButtons = document.querySelectorAll('.time-range-selector button');
        let revenueChart; // สร้างตัวแปรไว้นอกฟังก์ชันเพื่อเก็บ instance ของกราฟ

        // ฟังก์ชันสำหรับดึงข้อมูลและอัปเดตกราฟ
        function updateChart(period = 'week') {
            // ส่ง fetch request ไปยัง API พร้อมกับช่วงเวลาที่เลือก
            // fetch(`controls/test_grahp1.php?period=${period}`)
            fetch(`controls/test_grahp1.php`, {
                    method: 'POST',
                    headers: {
                        'Content-type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        period: period
                    })
                })
                .then(response => response.json())
                .then(chartData => {
                    // ถ้ามีกราฟเก่าอยู่ ให้ทำลายทิ้งก่อน
                    if (revenueChart) {
                        revenueChart.destroy();
                    }

                    // สร้างกราฟใหม่ด้วยข้อมูลที่ได้รับมา
                    revenueChart = new Chart(ctx, {
                        type: 'line', // หรือ 'bar'
                        data: {
                            labels: chartData.labels,
                            datasets: [{
                                label: 'รายได้',
                                data: chartData.data,
                                backgroundColor: 'rgba(30, 84, 112, 0.2)',
                                borderColor: 'rgba(30, 84, 112, 1)',
                                borderWidth: 2,
                                tension: 0.3,
                                fill: true
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return '฿' + value.toLocaleString();
                                        }
                                    }
                                }
                            },
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return `รายได้: ${context.formattedValue} บาท`;
                                        }
                                    }
                                }
                            }
                        }
                    });
                })
                .catch(error => console.error('Error fetching chart data:', error));
        }

        // จัดการการคลิกปุ่ม
        timeRangeButtons.forEach(button => {
            button.addEventListener('click', function() {
                timeRangeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const period = this.dataset.period;
                updateChart(period);
            });
        });

        // โหลดข้อมูลครั้งแรกเป็น 'สัปดาห์นี้'
        updateChart('week');
    });
    </script>
</body>

</html>