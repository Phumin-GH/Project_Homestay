<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Touch-Action Image Carousel</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: grid;
            place-items: center;
            min-height: 100vh;
            color: #333;
        }

        .main-container {
            max-width: 90%;
            width: 800px;
            text-align: center;
        }

        h1 {
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
        }

        p {
            margin-bottom: 2rem;
            color: #666;
        }

        .carousel-viewport {
            width: 100%;
            overflow: hidden;
            /* ซ่อนส่วนที่เกินออกไป */
            border-radius: 16px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            cursor: grab;
            /* เปลี่ยน cursor ให้รู้ว่าลากได้ */

            /* --- ✨ หัวใจของความลื่นไหลบนจอสัมผัส ✨ --- */
            touch-action: pan-y;
            /* บอกเบราว์เซอร์ว่า "จัดการแค่การ scroll แนวตั้งพอ" */
        }

        .carousel-viewport:active {
            cursor: grabbing;
        }

        .carousel-track {
            display: flex;
            /* ทำให้รูปเรียงต่อกันในแนวนอน */
            /* transition จะถูกเพิ่มโดย JavaScript เพื่อความสวยงามตอนปล่อย */
        }

        .slide {
            flex: 0 0 100%;
            /* แต่ละรูปมีความกว้าง 100% ของ viewport */
            width: 100%;
            aspect-ratio: 16 / 9;
            /* กำหนดสัดส่วนภาพ */
        }

        .slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            pointer-events: none;
            /* ป้องกันการลากรูปภาพโดยตรง */
        }
    </style>
</head>

<body>

    <div class="main-container">
        <h1>Smooth Image Carousel</h1>
        <p>Powered by CSS <code>touch-action</code> for a smooth mobile experience.</p>

        <div class="carousel-viewport">
            <div class="carousel-track">
                <div class="slide"><img
                        src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzNjAzNTV8MHwxfGFsbHx8fHx8fHx8fDE3MjU1OTEyNjd8&ixlib=rb-4.0.3&q=80&w=1080"
                        alt="Image 1"></div>
                <div class="slide"><img
                        src="https://images.unsplash.com/photo-1501854140801-50d01698950b?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzNjAzNTV8MHwxfGFsbHx8fHx8fHx8fDE3MjU1OTEyODZ8&ixlib=rb-4.0.3&q=80&w=1080"
                        alt="Image 2"></div>
                <div class="slide"><img
                        src="https://images.unsplash.com/photo-1470770841072-f978cf4d019e?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzNjAzNTV8MHwxfGFsbHx8fHx8fHx8fDE3MjU1OTEzMDN8&ixlib=rb-4.0.3&q=80&w=1080"
                        alt="Image 3"></div>
                <div class="slide"><img
                        src="https://images.unsplash.com/photo-1592815162463-546419741e75?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3wzNjAzNTV8MHwxfGFsbHx8fHx8fHx8fDE3MjU1OTExMTN8&ixlib=rb-4.0.3&q=80&w=1080"
                        alt="Image 4"></div>
            </div>
        </div>
    </div>

    <script>
        const viewport = document.querySelector('.carousel-viewport');
        const track = document.querySelector('.carousel-track');
        const slides = document.querySelectorAll('.slide');
        const slideWidth = slides[0].offsetWidth;
        let currentIndex = 0;

        let isDragging = false;
        let startPos = 0;
        let currentTranslate = 0;
        let prevTranslate = 0;
        let animationID;

        viewport.addEventListener('pointerdown', startDrag);
        viewport.addEventListener('pointerup', endDrag);
        viewport.addEventListener('pointerleave', endDrag);
        viewport.addEventListener('pointermove', drag);

        function startDrag(event) {
            isDragging = true;
            startPos = event.clientX;
            track.style.transition = 'none'; // ปิด transition ตอนลาก
            animationID = requestAnimationFrame(animation);
        }

        function drag(event) {
            if (isDragging) {
                const currentPosition = event.clientX;
                currentTranslate = prevTranslate + currentPosition - startPos;
            }
        }

        function animation() {
            setSliderPosition();
            if (isDragging) requestAnimationFrame(animation);
        }

        function endDrag() {
            if (!isDragging) return;
            isDragging = false;
            cancelAnimationFrame(animationID);

            const movedBy = currentTranslate - prevTranslate;

            // Snap to the next or previous slide
            if (movedBy < -100 && currentIndex < slides.length - 1) {
                currentIndex += 1;
            }
            if (movedBy > 100 && currentIndex > 0) {
                currentIndex -= 1;
            }

            goToSlide(currentIndex);
        }

        function goToSlide(index) {
            track.style.transition = 'transform 0.4s ease-out';
            currentTranslate = index * -slideWidth;
            prevTranslate = currentTranslate;
            setSliderPosition();
        }

        function setSliderPosition() {
            track.style.transform = `translateX(${currentTranslate}px)`;
        }
    </script>
</body>

</html>