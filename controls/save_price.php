<?php
session_start();
if (isset($_POST['total_price']) && isset($_POST['booking_id']) && isset($_POST['method'])) {
    $_SESSION['total_price'] = $_POST['total_price'] ?? null;
    $_SESSION['booking_id'] = $_POST['booking_id'] ?? null;
    $_SESSION['method'] = $_POST['method'] ?? null;
    $method = $_SESSION['method'];
    if($method === 'credit-card'){
        echo "credit-card";
    }else if($method === 'qrcode'){
        echo "qrcode";
    }
} else {
    echo "no_price";
}