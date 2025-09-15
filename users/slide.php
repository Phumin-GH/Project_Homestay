.banner-slider {
width: 100%;
height: 550px;
position: relative;
overflow: hidden;
border-radius: 0 0 20px 20px;
box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.banner-slider img {
width: 100%;
height: 100%;
object-fit: cover;
display: none;
filter: brightness(0.8);
}

.banner-slider img.active {
display: block;
}

.slider-btn {
position: absolute;
top: 50%;
transform: translateY(-50%);
z-index: 10;
background-color: rgba(0, 0, 0, 0.3);
color: white;
border: 1px solid rgba(255, 255, 255, 0.4);
border-radius: 50%;
width: 50px;
height: 50px;
font-size: 24px;
cursor: pointer;
display: flex;
justify-content: center;
align-items: center;
transition: all 0.3s ease;
opacity: 0;
}

.banner-slider:hover .slider-btn {
opacity: 1;
}

.slider-btn:hover {
background-color: rgba(0, 0, 0, 0.6);
transform: translateY(-50%) scale(1.1);
}

.prev {
left: 25px;
}

.next {
right: 25px;
}

.banner-text {
position: absolute;
top: 50%;
left: 50%;
transform: translate(-50%, -50%);
text-align: center;
color: white;
z-index: 5;
width: 80%;
}

.banner-text h2 {
font-size: 3.5rem;
margin-bottom: 1rem;
text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
}

.banner-text p {
font-size: 1.3rem;
text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.7);
}