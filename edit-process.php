<?php
session_start();

// จำลองการแก้ไขข้อมูล (ในระบบจริงจะมีการเชื่อมกับฐานข้อมูล)
if (isset($_POST['save_edit'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);

    if ($firstname !== "" && $lastname !== "") {
        // จำลองว่าอัปเดตสำเร็จ
        $_SESSION['message'] = '<div class="alert alert-success">✅ ข้อมูลได้รับการอัปเดตเรียบร้อยแล้ว!</div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-danger">❌ เกิดข้อผิดพลาดในการอัปเดตข้อมูล!</div>';
    }

    // ย้อนกลับไปที่ฟอร์ม
    header("Location: edit-profile.php");
    exit();
}