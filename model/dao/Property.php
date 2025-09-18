<?php
class Property
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล

    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function get_House($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_email = ?");
        $stmt->execute([$email]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $this->conn->prepare("SELECT  p.Property_id, p.Property_name
    FROM Property p
    WHERE p.Host_id = ? AND p.Property_status = '1'
    ORDER BY p.Property_id ASC");
        $stmt->execute([$host['Host_id']]);
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $properties;
    }
    public function get_Property_Host() {}

    public function get_manageProperty($email)
    {
        // ดึง Host_id
        $stmt = $this->conn->prepare("SELECT Host_id FROM host WHERE Host_email = ?");
        $stmt->execute([$email]);
        $host_id = $stmt->fetchColumn(); // fetchColumn() ถูกต้อง
        $sql = "SELECT p.*, h.Host_firstname, h.Host_lastname 
            FROM Property p 
            INNER JOIN Host h ON p.Host_id = h.Host_id 
            WHERE h.Host_id = ? AND p.Property_status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$host_id]);
        $list_house = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $list_house;
    }

    public function get_ListProperty($email)
    {
        $stmt = $this->conn->prepare("SELECT Host_id FROM host WHERE Host_email = ?");
        $stmt->execute([$email]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $this->conn->prepare("SELECT  p.Property_id, p.Property_name
    FROM Property p
    WHERE p.Host_id = ? AND p.Property_status = '1'
    ORDER BY p.Property_id ASC
");
        $stmt->execute([$host['Host_id']]);
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $properties;
    }
    public function get_Property($property_id)
    {
        $stmt = $this->conn->prepare("SELECT p.*, h.Host_firstname, h.Host_lastname FROM property p 
        INNER JOIN host h ON p.Host_id = h.Host_id
        WHERE p.Property_id = ?");
        $stmt->execute([$property_id]);
        $property = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$property) {
            return "ไม่พบข้อมูลบ้านพัก!";
        }
        return $property;
    }
    public function get_Image($property_id)
    {
        $stmt = $this->conn->prepare("SELECT Pro_image FROM Pro_image WHERE Property_id = ?");
        $stmt->execute([$property_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $images;
    }

    public function get_rooms($property_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM room WHERE Property_id = ?");
        $stmt->execute([$property_id]);
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rooms;
    }

    public function show_House()
    {
        $stmt = $this->conn->prepare("SELECT * FROM Property INNER JOIN Host on Property.Host_id = Host.Host_id WHERE Property.Property_status = 1");
        $stmt->execute();
        $homestay = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $homestay;
    }

    public function showPropertys($property_id)
    {
        $sql = "SELECT * FROM property 
    
    WHERE Property_id = ?
    ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id]);
        $house = $stmt->fetch(PDO::FETCH_ASSOC);
        return $house;
    }
    public function get_RoomsWalkin($property_id)
    {
        $stmt = $this->conn->prepare("SELECT Room_id, Room_number,Room_price FROM room WHERE Property_id = ? AND Room_status = '0'");
        $stmt->execute([$property_id]);
        $room = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $room;
    }
    public function showRooms($property_id)
    {
        $sql = "SELECT p.*,r.* FROM property p
    INNER JOIN room r ON p.Property_id = r.Property_id
    WHERE p.Property_id = ?
    ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id]);
        $room = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $room;
    }
    public function add_Property($house_name, $province, $district, $subdistrict, $latitude, $longitude, $roomNums, $roomPrices, $roomCaps, $roomUtens)
    {
        //เช็กว่าอัปโหลดรูปมาหรือยัง
        if (isset($_FILES['singleImage']) && $_FILES['singleImage']['error'] === 0) {
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
                return "อัปโหลดรูปภาพล้มเหลว";
            }
        } else {
            return "ไม่สามารถอัปโหลดรูปภาพได้";
        }
        if (
            empty($house_name) || empty($province) || empty($district) ||
            empty($subdistrict) || empty($latitude) || empty($longitude) || empty($image['name'])
        ) {
            return "กรุณากรอกข้อมูลให้ครบ";
        }
        if (empty($errors)) {
            try {
                $email = $_SESSION["Host_email"];
                $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_email = ?");
                $stmt->execute([$email]);
                $host = $stmt->fetch(PDO::FETCH_ASSOC);
                $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM property WHERE Host_id = ?");
                $checkStmt->execute([$host['Host_id']]);
                $count = $checkStmt->fetchColumn();
                if ($count > 6) {
                    return "ไม่สามารถเพิ่มเกิน 6 หลัง";
                }
                if ($host['Host_Status'] == 'inactive') {
                    // $_SESSION['error'] = "ยังไม่สามารถลงทะเบียนได้.";
                    return "ยังไม่สามารถลงทะเบียนได้";
                } elseif ($host['Host_Status'] == 'pending_verify' || $host['Host_Status'] == 'active') {
                    $email = $_SESSION["Host_email"];
                    $stmt = $this->conn->prepare("INSERT INTO property 
    (Host_id, Property_name, Property_province, Property_district, Property_subdistrict, Property_latitude, Property_longitude, Property_image) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

                    $stmt->execute([$host['Host_id'], $house_name, $province, $district, $subdistrict, $latitude, $longitude, $images]);
                    $NewProperty = $this->conn->lastInsertId();
                    for ($i = 0; $i < count($roomNums); $i++) {
                        $num = $roomNums[$i];
                        $price = $roomPrices[$i];
                        $cap = $roomCaps[$i];
                        $uten = $roomUtens[$i];
                        $stmt = $this->conn->prepare("INSERT INTO room (Room_number, Room_price, Room_capacity, Room_utensils, Property_id) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$num, $price, $cap, $uten, $NewProperty]);
                    }
                    if (isset($_FILES['multi_image'])) {
                        $uploadDir = __DIR__ . '/../images/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                        foreach ($_FILES['multi_image']['tmp_name'] as $key => $tmpName) {
                            $fileName = $_FILES['multi_image']['name'][$key];
                            $uniqueName = uniqid('img_') . "_" . basename($fileName);
                            $targetPath = $uploadDir . $uniqueName;
                            if (move_uploaded_file($tmpName, $targetPath)) {
                                $stmt = $this->conn->prepare("INSERT INTO Pro_image (Property_id, Pro_image) VALUES (?, ?)");
                                $stmt->execute([$NewProperty, "images/" . $uniqueName]);
                            }
                        }
                    }
                    // echo json_encode(["success" => true, "message" => "ส่งข้อมูลแล้ว รอแอดมินอนุมัติ."]);
                    // exit();
                    return true;
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }
    }
    public function edit_Property($property_id, $house_name, $province, $district, $subdistrict, $latitude, $longitude, $roomIds, $roomNums, $roomPrices, $roomCaps, $roomUtens, $status_room)
    {
        try {
            //เช็กว่าอัปโหลดรูปมาหรือยัง
            if (!isset($_FILES['singleImage']) || $_FILES['singleImage']['error'] !== UPLOAD_ERR_OK) {
                //  ไม่มีไฟล์ใหม่ หรือมีไฟล์แต่ error → ดึงรูปเก่า
                $stmtImg = $this->conn->prepare("SELECT Property_image FROM property WHERE Property_id = ?");
                $stmtImg->execute([$property_id]);
                $images = $stmtImg->fetchColumn();
                if (!$images) {
                    return "ไม่มีรูปอัปโหลดและไม่มีรูปเก่า";
                }
            } else {
                //  มีการอัปโหลดไฟล์ใหม่
                $image = $_FILES['singleImage'];
                $uploadDir = __DIR__ . '/../../public/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $imageName = uniqid('img_') . "_" . basename($image['name']);
                $targetPath = $uploadDir . $imageName;
                if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                    $images = "images/" . $imageName;
                } else {
                    return "อัปโหลดรูปล้มเหลว";
                }
            }
            if (isset($_FILES['multi_image'])) {
                $stmt = $this->conn->prepare("SELECT COUNT(Pro_image) FROM pro_image WHERE Property_id =?");
                $stmt->execute([$property_id]);
                $Img_house = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($Img_house) {
                    $del = $this->conn->prepare("DELETE FROM Pro_image WHERE Property_id = ?");
                    $del->execute([$property_id]);
                }
                $uploadDir = __DIR__ . '/../../public/images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                foreach ($_FILES['multi_image']['tmp_name'] as $key => $tmpName) {
                    $fileName = $_FILES['multi_image']['name'][$key];
                    $uniqueName = uniqid('img_') . "_" . basename($fileName);
                    $targetPath = $uploadDir . $uniqueName;
                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $stmt = $this->conn->prepare("INSERT INTO Pro_image (Property_id, Pro_image) VALUES (?, ?)");
                        $stmt->execute([$property_id, "images/" . $uniqueName]);
                    }
                }
            }
        } catch (PDOException $e) {
            return $e->getMessage();
        }
        if (
            empty($house_name) || empty($province) || empty($district) ||
            empty($subdistrict) || empty($latitude) || empty($longitude)
        ) {
            return "กรุณากรอกข้อมูลให้ครบ.";
        }
        if (empty($errors)) {
            try {
                $email = $_SESSION["Host_email"];
                $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_email = ?");
                $stmt->execute([$email]);
                $host = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($host['Host_Status'] == 'pending_verify') {
                    return "ยังไม่สามารถลงทะเบียนได้.";
                } elseif ($host['Host_Status'] == 'active') {
                    $email = $_SESSION["Host_email"];
                    // สมมติว่าคุณมี $property_id ที่ส่งมาจาก form
                    $stmt = $this->conn->prepare("UPDATE property SET Property_name = ?,Property_province = ?,Property_district = ?,Property_subdistrict = ?,Property_latitude = ?,Property_longitude = ?, Property_image = ?
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
                        $room_id = $roomIds[$i] ?? null;
                        $num = $roomNums[$i];
                        $price = $roomPrices[$i];
                        $cap = $roomCaps[$i];
                        $uten = $roomUtens[$i];
                        $status = $status_room[$i];
                        $check_room = $this->conn->prepare("SELECT COUNT(*) FROM room WHERE Room_number = ? AND Property_id = ?");
                        $check_room->execute([$num, $property_id]);
                        $room_num = $check_room->fetchColumn();
                        if ($room_num <= 1) {
                            if (!empty($room_id)) {
                                // Update ข้อมูลห้องเดิม 
                                $stmt = $this->conn->prepare("UPDATE room SET Room_number=?,Room_status=?, Room_price=?, Room_capacity=?, Room_utensils=? WHERE Room_id=? AND Property_id=?");
                                $stmt->execute([$num, $status, $price, $cap, $uten, $room_id, $property_id]);
                            } else {
                                $stmt = $this->conn->prepare("INSERT INTO room (Room_number, Room_price, Room_capacity, Room_utensils, Property_id) VALUES (?, ?, ?, ?, ?)");
                                $stmt->execute([$num, $price, $cap, $uten, $property_id]);
                            }
                        } else {
                            return "ข้อมูลหมายเลขห้องพักซ้ำ";
                        }
                    }
                    return true;
                    // }
                } else {
                    return "สถานะเจ้าของบ้านไม่ถูกต้อง.";
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }
    }
}
