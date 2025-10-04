<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>ปฏิทินการจอง</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet" />
    <style>
    #calendar {
        width: 100%;
        margin: 0 auto;
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#calendarModal">
            ดูปฏิทินการจอง
        </button>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="calendarModal" tabindex="-1" aria-labelledby="calendarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calendarModalLabel">ปฏิทินการจอง</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'th',
            aspectRatio: 1.8,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
                // right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: 'test_data_cal.php',
            backgroundColor: '#ff5733',
            borderColor: '#c70039',
            textColor: '#fff'
        });

        // ⚡ Render Calendar เมื่อ Modal เปิด
        var modal = document.getElementById('calendarModal');
        modal.addEventListener('shown.bs.modal', function() {
            calendar.render();
        });
    });
    </script>
</body>

</html>