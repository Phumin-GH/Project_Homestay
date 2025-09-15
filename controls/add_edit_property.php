<?php
session_start();
header('Content-Type: application/json');
include '../config/db_connect.php';
require_once __DIR__ . '/../dao/Property.php';
if (empty($_SESSION['Host_email'])) {
    echo json_encode(["success" => false, "message" => "Pls Login"]);
    // header("Location: host-login.php");
    exit();
}
$propertyHandle = new Property($conn);
if (isset($_POST['add_property'])) {
    $errors = [];
    $house_name = trim($_POST['house_name']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $subdistrict = trim($_POST['subdistrict']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $roomNums = $_POST['roomNum'] ?? [];
    $roomPrices = $_POST['roomPrice'] ?? [];
    $roomCaps = $_POST['roomCap'] ?? [];
    $roomUtens = $_POST['roomUten'] ?? [];
    $add_Property = $propertyHandle->add_Property($house_name, $province, $district, $subdistrict, $latitude, $longitude, $roomNums, $roomPrices, $roomCaps, $roomUtens);
    if ($add_Property === true) {
        echo json_encode(["success" => true, "message" => "ส่งข้อมูลแล้ว รอแอดมินอนุมัติ."]);
        exit();
    } else {
        $_SESSION['msg'] = $add_Propertyใ;
    }
    // เช็กว่าอัปโหลดรูปมาหรือยัง
    // if (isset($_FILES['singleImage']) && $_FILES['singleImage']['error'] === 0) {
    //     $image = $_FILES['singleImage'];
    //     $uploadDir = __DIR__ . '/../images/';
    //     if (!is_dir($uploadDir)) {
    //         mkdir($uploadDir, 0777, true);
    //     }
    //     $imageName = uniqid('img_') . "_" . basename($image['name']);
    //     $targetPath = $uploadDir . $imageName;
    //     if (move_uploaded_file($image['tmp_name'], $targetPath)) {
    //         $images = "images/" . $imageName;
    //     } else {
    //         echo json_encode(["success" => false, "message" => "อัปโหลดรูปภาพล้มเหลว."]);
    //         exit();
    //         // header("Location: ../hosts/add-property.php");
    //     }
    //     if (isset($_FILES['multi_image'])) {
    //         $uploadDir = __DIR__ . '/../images/';
    //         if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    //         foreach ($_FILES['multi_image']['tmp_name'] as $key => $tmpName) {
    //             $fileName = $_FILES['multi_image']['name'][$key];
    //             $uniqueName = uniqid('img_') . "_" . basename($fileName);
    //             $targetPath = $uploadDir . $uniqueName;

    //             if (move_uploaded_file($tmpName, $targetPath)) {

    //                 $stmt = $conn->prepare("INSERT INTO Pro_image (Property_id, Pro_image) VALUES (?, ?)");
    //                 $stmt->execute([$property_id, "images/" . $uniqueName]);
    //             }
    //         }
    //     }
    // } else {
    //     echo json_encode(["success" => false, "message" => "No image uploaded."]);
    //     exit();
    // }
    // if (
    //     empty($house_name) || empty($province) || empty($district) ||
    //     empty($subdistrict) || empty($latitude) || empty($longitude) || empty($image['name'])
    // ) {
    //     echo json_encode(["success" => false, "message" => "กรุณากรอกข้อมูลให้ครบ."]);
    //     exit();
    // }
    // if (empty($errors)) {
    //     try {
    //         $email = $_SESSION["Host_email"];
    //         $stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ?");
    //         $stmt->execute([$email]);
    //         $host = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $checkStmt = $conn->prepare("SELECT COUNT(*) FROM property WHERE Host_id = ?");
    //         $checkStmt->execute([$host['Host_id']]);
    //         $count = $checkStmt->fetchColumn();
    //         if ($count > 6) {
    //             $_SESSION['error'] = "ไม่สามารถเกิน 5 หลัง.";
    //             // header("Location: ../hosts/add-property.php");
    //             echo json_encode(["success" => false, "message" => "ไม่สามารถเกิน 5 หลัง."]);
    //             exit();
    //         }
    //         if ($host['Host_Status'] == 'inactive') {
    //             $_SESSION['error'] = "ยังไม่สามารถลงทะเบียนได้.";
    //             // header("Location: ../hosts/host-dashboard.php");
    //             echo json_encode(["success" => false, "message" => "ยังไม่สามารถลงทะเบียนได้."]);
    //             exit();
    //         } elseif ($host['Host_Status'] == 'pending_verify' || $host['Host_Status'] == 'active') {
    //             $email = $_SESSION["Host_email"];
    //             $stmt = $conn->prepare("INSERT INTO property 
    // (Host_id, Property_name, Property_province, Property_district, Property_subdistrict, Property_latitude, Property_longitude, Property_image) 
    // VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    //             $stmt->execute([$host['Host_id'], $house_name, $province, $district, $subdistrict, $latitude, $longitude, $images]);
    //             $NewProperty = $conn->lastInsertId();
    //             for ($i = 0; $i < count($roomNums); $i++) {
    //                 $num = $roomNums[$i];
    //                 $price = $roomPrices[$i];
    //                 $cap = $roomCaps[$i];
    //                 $uten = $roomUtens[$i];
    //                 $stmt = $conn->prepare("INSERT INTO room (Room_number, Room_price, Room_capacity, Room_utensils, Property_id) VALUES (?, ?, ?, ?, ?)");
    //                 $stmt->execute([$num, $price, $cap, $uten, $NewProperty]);
    //             }
    //             $_SESSION['error'] = "ส่งข้อมูลแล้ว รอแอดมินอนุมัติ.";
    //             // header("Location: ../hosts/host-dashboard.php");
    //             echo json_encode(["success" => true, "message" => "ส่งข้อมูลแล้ว รอแอดมินอนุมัติ."]);
    //             exit();
    //         }
    //     } catch (PDOException $e) {
    //         $_SESSION['error'] = "ฐานข้อมูล: " . $e->getMessage();
    //         echo json_encode(["success" => false, "message" => "ฐานข้อมูล: " . $e->getMessage()]);
    //         exit();
    //         // header("Location: ../hosts/add-property.php");
    //     }
    // }
}

if (isset($_POST['edit_property'])) {
    $errors = [];
    $property_id = trim($_POST['property_id']);
    $house_name = trim($_POST['house_name']);
    $province = trim($_POST['province']);
    $district = trim($_POST['district']);
    $subdistrict = trim($_POST['subdistrict']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $roomIds = $_POST['roomId'] ?? [];
    $roomNums = $_POST['roomNum'] ?? [];
    $roomPrices = $_POST['roomPrice'] ?? [];
    $roomCaps = $_POST['roomCap'] ?? [];
    $roomUtens = $_POST['roomUten'] ?? [];
    // เช็กว่าอัปโหลดรูปมาหรือยัง
    try {
        if (!isset($_FILES['singleImage']) || $_FILES['singleImage']['error'] !== UPLOAD_ERR_OK) {
            //  ไม่มีไฟล์ใหม่ หรือมีไฟล์แต่ error → ดึงรูปเก่า
            $stmtImg = $conn->prepare("SELECT Property_image FROM property WHERE Property_id = ?");
            $stmtImg->execute([$property_id]);
            $images = $stmtImg->fetchColumn();

            if (!$images) {
                echo json_encode(['success' => false, 'message' => 'No image uploaded and no old image found.']);
                exit();
            }
        } else {
            //  มีการอัปโหลดไฟล์ใหม่
            $image = $_FILES['singleImage'];
            $uploadDir = __DIR__ . '/../images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $imageName = uniqid('img_') . "_" . basename($image['name']);
            $targetPath = $uploadDir . $imageName;

            if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                $images = "images/" . $imageName;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload image.']);
                exit();
            }
        }
        $stmt = $conn->prepare("SELECT COUNT(Pro_image) FROM pro_image WHERE Property_id =?");
        $stmt->execute([$property_id]);
        $Img_house = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($Img_house) {
            $del = $conn->prepare("DELETE FROM Pro_image WHERE Property_id = ?");
            $del->execute([$property_id]);
        }
        if (isset($_FILES['multi_image'])) {
            $uploadDir = __DIR__ . '/../images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            foreach ($_FILES['multi_image']['tmp_name'] as $key => $tmpName) {
                $fileName = $_FILES['multi_image']['name'][$key];
                $uniqueName = uniqid('img_') . "_" . basename($fileName);
                $targetPath = $uploadDir . $uniqueName;

                if (move_uploaded_file($tmpName, $targetPath)) {

                    $stmt = $conn->prepare("INSERT INTO Pro_image (Property_id, Pro_image) VALUES (?, ?)");
                    $stmt->execute([$property_id, "images/" . $uniqueName]);
                }
            }
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
        exit();
    }
    if (
        empty($house_name) || empty($province) || empty($district) ||
        empty($subdistrict) || empty($latitude) || empty($longitude)
    ) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลให้ครบ.']);
        exit();
    }
    if (empty($errors)) {
        try {
            $email = $_SESSION["Host_email"];
            $stmt = $conn->prepare("SELECT * FROM host WHERE Host_email = ?");
            $stmt->execute([$email]);
            $host = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($host['Host_Status'] == 'pending_verify') {
                // $_SESSION['error']= "ยังไม่สามารถลงทะเบียนได้.";
                echo json_encode(['success' => false, 'message' => 'ยังไม่สามารถลงทะเบียนได้.']);
                exit();
            } elseif ($host['Host_Status'] == 'active') {
                $email = $_SESSION["Host_email"];
                // สมมติว่าคุณมี $property_id ที่ส่งมาจาก form
                $stmt = $conn->prepare("UPDATE property SET Property_name = ?,Property_province = ?,Property_district = ?,Property_subdistrict = ?,Property_latitude = ?,Property_longitude = ?, Property_image = ?
                                            WHERE Property_id = ? 
                                            AND Host_id = ?"); // เผื่อป้องกันคนอื่นแก้ไข

                $stmt->execute([
                    $house_name,
                    $province,
                    $district,
                    $subdistrict,
                    $latitude,
                    $longitude,
                    $images,
                    $property_id, // id บ้านพักที่แก้ไข
                    $host['Host_id'] // ตรวจสอบเจ้าของ
                ]);
                for ($i = 0; $i < count($roomNums); $i++) {
                    $id = $roomIds[$i] ?? null;
                    $num = $roomNums[$i];
                    $price = $roomPrices[$i];
                    $cap = $roomCaps[$i];
                    $uten = $roomUtens[$i];
                    if (!empty($id)) {
                        // ✅ Update ห้องเดิม 
                        $stmt = $conn->prepare("UPDATE room SET Room_number=?, Room_price=?, Room_capacity=?, Room_utensils=? WHERE Room_id=? AND Property_id=?");
                        $stmt->execute([$num, $price, $cap, $uten, $id, $property_id]);
                    } else {
                        $stmt = $conn->prepare("INSERT INTO room (Room_number, Room_price, Room_capacity, Room_utensils, Property_id) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$num, $price, $cap, $uten, $property_id]);
                    }
                }

                echo json_encode(['success' => true, 'message' => "แก้ไขข้อมูลบ้านพักเรียบร้อยแล้ว"]);
                exit();
                // }
            } else {
                echo json_encode(['success' => false, 'message' => 'สถานะเจ้าของบ้านไม่ถูกต้อง.']);
                exit();
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => "ฐานข้อมูล: " . $e->getMessage()]);
            exit();
            // $_SESSION['error']= "ฐานข้อมูล: " . $e->getMessage();
            // header("Location: ../hosts/manage-property.php");
        }
    }
}
