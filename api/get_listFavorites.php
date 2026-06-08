<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Favorites.php';
// ตรวจสอบว่ามี User_email ใน session หรือไม่
if (isset($_SESSION["User_email"])) {
    $email = $_SESSION["User_email"];
    $listHandler = new Favorites($conn);
    $favorites = $listHandler->get_listFavorites($email);
    if ($favorites === false) {
        $_SESSION['err'] = "ไม่พบข้อมูลอีเมล";
    }
    $fav_btn = $listHandler->show_Favorites($email);
} else {
    // ถ้าไม่มี session ให้กำหนดค่าเริ่มต้น
    $favorites = array();
    $fav_btn = array();
}
