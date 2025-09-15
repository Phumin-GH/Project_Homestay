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
    ORDER BY p.Property_id ASC
");
        $stmt->execute([$host['Host_id']]);
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $properties;
    }

    public function get_manageProperty($email)
    {
        if (empty($email)) {
            return false;
        }
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

        return $list_house; // ถ้าไม่มี property → return array ว่าง
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
                echo json_encode(["success" => false, "message" => "อัปโหลดรูปภาพล้มเหลว."]);
                exit();
                // header("Location: ../hosts/add-property.php");
            }
            if (isset($_FILES['multi_image'])) {
                $uploadDir = __DIR__ . '/../images/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                foreach ($_FILES['multi_image']['tmp_name'] as $key => $tmpName) {
                    $fileName = $_FILES['multi_image']['name'][$key];
                    $uniqueName = uniqid('img_') . "_" . basename($fileName);
                    $targetPath = $uploadDir . $uniqueName;

                    // if (move_uploaded_file($tmpName, $targetPath)) {

                    //     $stmt = $this->conn->prepare("INSERT INTO Pro_image (Property_id, Pro_image) VALUES (?, ?)");
                    //     $stmt->execute([$property_id, "images/" . $uniqueName]);
                    // }
                }
            }
        } else {
            return "ไม่พบรูปที่อัปโหลด";
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
                    return "ไม่สามารถเกิน 5 หลัง.";
                }
                if ($host['Host_Status'] == 'inactive') {
                    return "ยังไม่สามารถลงทะเบียนได้.";
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
                    return true;
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }
    }
    public function edit_Property() {}
}
