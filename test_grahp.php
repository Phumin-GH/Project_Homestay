<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ปฏิทินใน Modal</title>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            font-family: sans-serif;
            padding: 2rem;
        }

        /* --- 1. CSS สำหรับ Modal --- */
        /* พื้นหลังสีดำโปร่งแสง */
        .modal-overlay {
            display: none;
            /* ซ่อนไว้ในตอนแรก */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
        }

        /* กล่องเนื้อหาของ Modal */
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            /* จัดให้อยู่กึ่งกลาง */
            padding: 25px;
            border: 1px solid #888;
            width: 90%;
            max-width: 1100px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        /* ปุ่มปิด (X) */
        .modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .modal-close:hover,
        .modal-close:focus {
            color: black;
        }

        /* สไตล์ปุ่มกด */
        .show-calendar-btn {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #1e5470;
            color: white;
            border: none;
            border-radius: 8px;
        }
    </style>
</head>

<body>

    <button id="openModalBtn" class="show-calendar-btn">
        <i class="fas fa-calendar-alt"></i> แสดงปฏิทินการจอง
    </button>

    <div id="calendarModal" class="modal-overlay">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <div id="calendar"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 3. ดึง Element ที่จำเป็น ---
            const modal = document.getElementById('calendarModal');
            const openBtn = document.getElementById('openModalBtn');
            const closeBtn = document.querySelector('.modal-close');
            const calendarEl = document.getElementById('calendar');

            let calendarInstance = null; // ตัวแปรสำหรับเก็บ instance ของปฏิทิน

            // --- ฟังก์ชันเปิด Modal ---
            function openModal() {
                modal.style.display = 'block';

                // ตรวจสอบและสร้างปฏิทิน (เฉพาะครั้งแรกที่เปิด)
                if (!calendarInstance) {
                    calendarInstance = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'th',
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek'
                        },
                        // ✨ สามารถดึงข้อมูลแบบไดนามิกได้เหมือนเดิม ✨
                        events: 'test_grahp1.php'
                    });
                    calendarInstance.render();
                } else {
                    // ถ้าปฏิทินเคยถูกสร้างแล้ว ให้ปรับขนาดใหม่เผื่อหน้าจอเปลี่ยน
                    calendarInstance.updateSize();
                }
            }

            // --- ฟังก์ชันปิด Modal ---
            function closeModal() {
                modal.style.display = 'none';
            }

            // --- 4. ผูก Event Listeners ---
            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);

            // ปิด Modal เมื่อคลิกที่พื้นหลังสีดำ
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    closeModal();
                }
            });
        });
    </script>

</body>

</html>