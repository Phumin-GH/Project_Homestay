<?php
session_start();
include '../config/db_connect.php'; 

if (empty($_SESSION['Host_email'])) {
    header("Location: host-login.php");
    exit();
}



if (isset($_POST['add_property'])) {
    $house_name = trim($_POST['house_name']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $subdistrict = trim($_POST['subdistrict']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
   // เช็กว่าอัปโหลดรูปมาหรือยัง
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $image = $_FILES['image'];

    $uploadDir = __DIR__ . '/../images/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $imageName = uniqid('img_') . "_" . basename($image['name']);
    $targetPath = $uploadDir . $imageName;

    if (move_uploaded_file($image['tmp_name'], $targetPath)) {
        $images = "images/" . $imageName;
        
    } else {
        $_SESSION['error']= "อัปโหลดรูปภาพล้มเหลว.";
        header("Location: ../hosts/add-property.php");
    }
} else {
    $_SESSION['error']="No image uploaded.";
    header("Location: ../hosts/add-property.php");
}
    // Validate inputs
    if (
        empty($house_name) || empty($province) || empty($district) ||
        empty($subdistrict) || empty($latitude) || empty($longitude) || empty($image['name'])
    ) {
        $_SESSION['error']= "กรุณากรอกข้อมูลให้ครบ.";
        header("Location: ../hosts/add-property.php");
    }

    if (empty($errors)) {
        try {
            $email = $_SESSION["Host_email"];
            $stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ?");
            $stmt->execute([$email]);
            $host = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM host WHERE Host_email = ?");
            $checkStmt->execute([$email]);
            $count = $checkStmt->fetchColumn();

            if($count > 0){
                $_SESSION['error']= "ไม่สามารถเกิน 1 หลัง.";
                header("Location: ../hosts/add-property.php");
                exit();
            }else if($host['Host_Status'] == 0){
                $_SESSION['error']= "ยังไม่สามารถลงทะเบียนได้.";
                header("Location: ../hosts/add-property.php");
                exit();
            }else if($host['Host_Status'] == 1){
                $email = $_SESSION["Host_email"];
                $stmt = $conn->prepare("INSERT INTO  property(Host_id,Property_name,Property_province,Property_district,Property_subdistrict,Property_latitude,Property_longitude,Property_image) VALUE (?,?,?,?,?,?,?,?)");
                $stmt->execute([$host['Host_id'],$house_name, $province, $district,$subdistrict, $latitude, $longitude,$images]);
                // $host = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['error']= "รอแอดมินอนุมัติ";
                header("Location: ../hosts/add-property.php");
                exit();
            }
            
            
        } catch (PDOException $e) {
            $_SESSION['error']= "ฐานข้อมูล: " . $e->getMessage();
            header("Location: ../hosts/add-property.php");
        }
    }
}

?>