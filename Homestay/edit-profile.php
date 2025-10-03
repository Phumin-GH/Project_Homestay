<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Profile</title>
    <style>
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: bold;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    </style>
</head>

<body>

    <h2>Edit Profile Form</h2>

    <!-- แสดง Alert -->
    <?php 
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
    unset($_SESSION['message']); // เคลียร์ข้อความหลังแสดงแล้ว
}
?>

    <!-- แบบฟอร์มส่งไปที่ edit-process.php -->
    <form action="edit-process.php" method="POST">
        <label>First Name:</label><br>
        <input type="text" name="firstname" required><br><br>

        <label>Last Name:</label><br>
        <input type="text" name="lastname" required><br><br>

        <button type="submit" name="save_edit">Save</button>

    </form>
    <form action="test.php" method="post" enctype="multipart/form-data">
        <input type="file" name="image">
        <button type="submit">Upload</button>
    </form>



</body>

</html>