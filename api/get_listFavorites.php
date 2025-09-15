<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once  __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/Favorites.php';
$email = $_SESSION["User_email"];
$listHandler = new Favorites($conn);
$favorites = $listHandler->get_listFavorites($email);
if ($favorites === false) {
    $_SESSION['err'] = "ไม่พบข้อมูลอีเมล";
}
$fav_btn = $listHandler->show_Favorites($email);
