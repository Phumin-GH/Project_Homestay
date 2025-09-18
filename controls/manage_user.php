<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. เรียกใช้ไฟล์ที่จำเป็น
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../dao/User.php';
if (isset($_SESSION['user_id'])) {
}
