<?php
class Property
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล

    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function total_property()
    {
        $sql = "SELECT COUNT(*) FROM property";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $total_property = $stmt->fetchColumn();
        return $total_property;
    }
    public function Total_properties($email)
    {
        $host_id = $this->get_host_id($email);
        $sql = "SELECT COUNT(*) FROM property WHERE Host_id = ? AND Property_status = 'approve'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$host_id]);
        $total_properties = $stmt->fetchColumn();
        return $total_properties;
    }
    public function Total_income($email)
    {
        $host_id = $this->get_host_id($email);
        $sql = "SELECT SUM(Host_payout) FROM transactions t
        INNER JOIN booking b ON t.Booking_id = b.Booking_id
        INNER JOIN property p ON b.Property_id = p.Property_id
        INNER JOIN host h ON p.host_id = h.host_id
        WHERE p.Host_id =? AND p.Property_status = 'approve'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$host_id]);
        $total_booking = $stmt->fetchColumn();
        return $total_booking;
    }
    public function Total_reviews($email)
    {
        $host_id = $this->get_host_id($email);
        $sql = "SELECT COUNT(*) FROM review rv INNER JOIN  property p ON rv.Property_id = p.Property_id
        INNER JOIN host h ON p.Host_id = h.Host_id 
        WHERE p.Host_id =? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$host_id]);
        $total_reviews = $stmt->fetchColumn();
        return $total_reviews;
    }
    public function Total_booking($email)
    {
        $host_id = $this->get_host_id($email);
        $sql = "SELECT COUNT(*) FROM booking b LEFT JOIN  property p ON b.Property_id = p.Property_id
        INNER JOIN host h ON h.Host_id = p.Host_id 
        WHERE p.Host_id =? AND p.Property_status = 'approve'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$host_id]);
        $total_booking = $stmt->fetchColumn();
        return $total_booking;
    }
    public function get_host_id($email)
    {
        $sql = "SELECT Host_id FROM host WHERE Host_email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $host_id = $stmt->fetchColumn();
        return $host_id;
    }
    public function search_house($searchTerm)
    {
        switch ($searchTerm) {
            case 'best_reviews':
                $sql = "SELECT p.Property_id,p.Property_name,p.Property_province, 
                p.Property_district,p.Property_subdistrict,p.Property_image, 
                h.Host_firstname,h.Host_lastname,p.Property_image, 
                AVG(rev.Rating) AS Rating, 
                COUNT(rev.Review_id) AS review_count 
                FROM property p 
                INNER JOIN review rev ON p.Property_id = rev.Property_id 
                LEFT JOIN host h ON p.Host_id = h.Host_id 
                WHERE p.Property_status = 'approve' 
                GROUP BY p.Property_id,p.Property_name,p.Property_province, 
                p.Property_district,p.Property_subdistrict,p.Property_image, 
                h.Host_firstname,h.Host_lastname,p.Property_image 
                HAVING AVG(rev.Rating) >= 3 
                ORDER BY Rating DESC LIMIT 10";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'rate_poor';
                $sql = "SELECT p.Property_id,p.Property_name,p.Property_province, 
                p.Property_district,p.Property_subdistrict,p.Property_image, 
                h.Host_firstname,h.Host_lastname,p.Property_image, 
                AVG(rv.Rating) AS Rating, AVG(r.Room_price) AS avg_price, 
                COUNT(rv.Review_id) AS review_count 
                FROM property p 
                INNER JOIN host h on p.Host_id = h.Host_id 
                LEFT JOIN review rv on p.Property_id = rv.Property_id 
                INNER JOIN room r on p.Property_id = r.Property_id 
                WHERE p.Property_status = 'approve' 
                GROUP BY p.Property_id,p.Property_name,p.Property_province, 
                p.Property_district,p.Property_subdistrict,p.Property_image,
                 h.Host_firstname,h.Host_lastname,p.Property_image 
                HAVING AVG(r.Room_price) <= 1000 
                ORDER BY avg_price ASC
                LIMIT 10";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'popular':
                $sql = "SELECT
    p.Property_id,p.Property_name,p.Property_province,
    p.Property_district,p.Property_subdistrict,p.Property_image,
    h.Host_firstname,h.Host_lastname,
    COALESCE(rs.avg_rating, 0) AS Rating,           
    COALESCE(bc.total_bookings, 0) AS booking_count,
    COALESCE(rs.review_count, 0) AS review_count
    FROM property p
    INNER JOIN host h ON p.Host_id = h.Host_id
    LEFT JOIN (
    SELECT 
        Property_id, 
        COUNT(*) AS total_bookings
    FROM (
        SELECT Property_id FROM booking WHERE Booking_status = 'successful'
        UNION ALL
        SELECT Property_id FROM walkin WHERE Walkin_status = 'successful'
    ) AS all_bookings
        GROUP BY Property_id
    ) AS bc ON p.Property_id = bc.Property_id
    LEFT JOIN (
    SELECT
        Property_id,
        AVG(Rating) AS avg_rating,
        COUNT(Review_id) AS review_count
    FROM review
    GROUP BY Property_id
    ) AS rs ON p.Property_id = rs.Property_id
    WHERE p.Property_status = 'approve'
    ORDER BY booking_count DESC LIMIT 10;";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute([]);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
            default:
                $sql = "SELECT p.Property_id,p.Property_name,p.Property_province,
p.Property_district,p.Property_subdistrict,p.Property_image,
h.Host_firstname,h.Host_lastname,p.Property_image,
AVG(rv.Rating) AS Rating,
COUNT(rv.Review_id) AS review_count 
        FROM property p
        INNER JOIN host h on p.Host_id = h.Host_id
        LEFT JOIN review rv on p.Property_id = rv.Property_id 
        WHERE p.Property_status = 'approve'";
                $params = [];

                $sql .= "AND( p.Property_name LIKE ? OR p.Property_province LIKE ?)";
                $likeTerm = "%" . $searchTerm . "%";
                $params[] = $likeTerm;
                $params[] = $likeTerm;

                $sql .= "GROUP BY p.Property_id,p.Property_name,p.Property_province,p.Property_district,p.Property_subdistrict,h.Host_firstname,
        h.Host_lastname,p.Property_image";
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        }
        return $products;
    }
    public function room_calendar($property_id)
    {
        $sql = "SELECT Booking_id,Room_number, User_id, Check_in, Check_out 
        FROM booking 
        WHERE Property_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id]);
        $bookings_from_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $events = [];
        foreach ($bookings_from_db as $booking) {
            $events[] = [
                'title' => 'ห้อง' . $booking['Room_number'],
                'start' => $booking['Check_in'],
                'end' => $booking['Check_out'],
                'allDay' => true,

            ];
        }
        return $events;
    }
    public function get_AllProperty()
    {
        $sql = "SELECT p.*,h.Host_firstname,h.Host_lastname FROM property p
        LEFT JOIN host h ON p.Host_id = h.Host_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([]);
        $AllProperty = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $AllProperty;
    }
    public function get_manageProperty($email)
    {
        // ดึง Host_id
        $stmt = $this->conn->prepare("SELECT Host_id FROM host WHERE Host_email = ?");
        $stmt->execute([$email]);
        $host_id = $stmt->fetchColumn(); // fetchColumn() ถูกต้อง
        $sql = "SELECT p.*, h.Host_firstname, h.Host_lastname 
            FROM Property p 
            INNER JOIN Host h ON p.Host_id = h.Host_id 
            WHERE h.Host_id = ? AND p.Property_status = 'approve'";
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
    WHERE p.Host_id = ? AND p.Property_status = 'approve'
    ORDER BY p.Property_id ASC
");
        $stmt->execute([$host['Host_id']]);
        $properties = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $properties;
    }
    public function get_Property($property_id)
    {
        $stmt = $this->conn->prepare("SELECT AVG(rv.Rating) as Rating ,p.*, 
        h.Host_firstname, h.Host_lastname ,sv.Services_name,
        sv.Services_description,atv.Activity_name,atv.Activity_description
        FROM property p 
        INNER JOIN host h ON p.Host_id = h.Host_id
        INNER JOIN review rv ON p.Property_id = rv.Property_id
        LEFT JOIN services sv ON p.Property_id = sv.Property_id
        LEFT JOIN activity atv ON p.Property_id = atv.Property_id
        WHERE p.Property_id = ?
        GROUP BY h.Host_firstname,h.Host_lastname
        ");
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
        $stmt = $this->conn->prepare("SELECT p.Property_id,p.Property_name,p.Property_province,p.Property_district,p.Property_subdistrict,h.Host_firstname,h.Host_lastname,p.Property_image,AVG(rv.Rating) AS Rating 
        FROM property p
        INNER JOIN host h on p.Host_id = h.Host_id
        LEFT JOIN review rv on p.Property_id = rv.Property_id 
        WHERE p.Property_status = 'approve'
        GROUP BY p.Property_id,p.Property_name,p.Property_province,p.Property_district,p.Property_subdistrict,h.Host_firstname,h.Host_lastname,p.Property_image");
        $stmt->execute();
        $homestay = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $homestay;
    }

    public function showPropertys($property_id)
    {
        $sql = "SELECT p.*,sv.Services_name,
        sv.Services_description,atv.Activity_name,atv.Activity_description 
        FROM property p
    LEFT JOIN services sv ON p.Property_id = sv.Property_id
    LEFT JOIN activity atv ON p.Property_id = atv.Property_id
    WHERE p.Property_id = ?
    ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id]);
        $house = $stmt->fetch(PDO::FETCH_ASSOC);
        return $house;
    }
    public function get_RoomsWalkin($property_id)
    {
        $stmt = $this->conn->prepare("SELECT Room_id, Room_number,Room_price FROM room WHERE Property_id = ? AND Room_status = 'Available'");
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
    public function add_Property($house_name, $province, $district, $subdistrict, $latitude, $longitude, $roomNums, $roomPrices, $roomCaps, $roomUtens, $Name_Service, $Des_Service, $Name_Activity, $Des_Activity)
    {
        //เช็กว่าอัปโหลดรูปมาหรือยัง
        if (isset($_FILES['singleImage']) && $_FILES['singleImage']['error'] === 0) {
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
                return "อัปโหลดรูปภาพล้มเหลว";
            }
        } else {
            return "ไม่สามารถอัปโหลดรูปภาพได้";
        }
        if (
            empty($house_name) || empty($province) || empty($district) ||
            empty($subdistrict) || empty($latitude) || empty($longitude) ||
            empty($image['name'])
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
                    if (isset($Des_Activity) || isset($Des_Service) || isset($Name_Activity) || isset($Name_Service)) {
                        try {

                            $sql_atv = "INSERT INTO  services 
                    (Property_id,Services_name,Services_description) VALUES (?,?,?)
                    ";
                            $stmt = $this->conn->prepare($sql_atv);
                            $stmt->execute([

                                $NewProperty,
                                $Name_Activity,
                                $Des_Activity
                            ]);
                            $sql_sv = "INSERT INTO activity
                    (Property_id,Activity_name,Activity_description)
                    VALUES(?,?,?)";
                            $stmt = $this->conn->prepare($sql_sv);
                            $stmt->execute([
                                $NewProperty,
                                $Name_Service,
                                $Des_Service
                            ]);
                        } catch (PDOException $e) {

                            return $e->getMessage();
                        }
                    }
                    if (isset($_FILES['multi_image'])) {
                        $uploadDir = __DIR__ . '/../../public/images/';
                        if (!is_dir($uploadDir))
                            mkdir($uploadDir, 0777, true);
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
                    return true;
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }
    }
    public function Calendar_Room($property_id)
    {
        $sql = "SELECT b.Booking_id,r.Room_number,b.Room_id, b.User_id, b.Check_in, b.Check_out 
        FROM booking b
        INNER JOIN room r ON b.Room_id = r.Room_id
        WHERE b.Property_id = $property_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $bookings_from_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $bookings_from_db;
    }
    public function edit_Property($property_id, $house_name, $province, $district, $subdistrict, $latitude, $longitude, $roomIds, $roomNums, $roomPrices, $roomCaps, $roomUtens, $status_room, $Name_Service, $Des_Service, $Name_Activity, $Des_Activity)
    {
        try {
            if (!isset($_FILES['singleImage']) || $_FILES['singleImage']['error'] !== UPLOAD_ERR_OK) {
                $stmtImg = $this->conn->prepare("SELECT Property_image FROM property WHERE Property_id = ?");
                $stmtImg->execute([$property_id]);
                $images = $stmtImg->fetchColumn();
                if (!$images) {
                    return "ไม่มีรูปอัปโหลดและไม่มีรูปเก่า";
                }
            } else {
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
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);
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
                    $stmt = $this->conn->prepare("UPDATE property 
                    SET Property_name = ?,Property_province = ?,Property_district = ?,Property_subdistrict = ?,
                    Property_latitude = ?,Property_longitude = ?, Property_image = ?
                    WHERE Property_id = ? AND Host_id = ?");
                    $stmt->execute([
                        $house_name,
                        $province,
                        $district,
                        $subdistrict,
                        $latitude,
                        $longitude,
                        $images,
                        $property_id,
                        $host['Host_id']
                    ]);
                    // if (!empty($Name_Activity)) {
                    //     $sql_sv = "UPDATE services
                    // SET Services_name=?,Services_description=?
                    // WHERE Property_id = ? ";
                    //     $stmt_sv = $this->conn->prepare($sql_sv);
                    //     try {
                    //         $stmt_sv->execute([
                    //             $Name_Service,
                    //             $Des_Service,
                    //             $property_id
                    //         ]);
                    //     } catch (PDOException $e) {
                    //         return $e->getMessage();
                    //     }
                    // }
                    // if (!empty($Name_Activity)) {
                    //     $sql_atv = "UPDATE activity
                    // SET Services_name=?,Services_description=?
                    // WHERE Property_id = ? ";
                    //     $stmt_atv = $this->conn->prepare($sql_atv);
                    //     try {
                    //         $stmt_atv->execute([

                    //             $Name_Activity,
                    //             $Des_Activity,
                    //             $property_id
                    //         ]);
                    //     } catch (PDOException $e) {
                    //         return $e->getMessage();
                    //     }
                    // }
                    for ($i = 0; $i < count($roomNums); $i++) {
                        $room_id = $roomIds[$i] ?? null;
                        $num = $roomNums[$i];
                        $price = $roomPrices[$i];
                        $cap = $roomCaps[$i];
                        $uten = $roomUtens[$i];
                        $status = $status_room[$i];
                        if (!empty($room_id)) {
                            $check_sql = "SELECT COUNT(*) FROM room WHERE Room_number = ? AND Property_id = ? AND Room_id != ?";
                            $check_stmt = $this->conn->prepare($check_sql);
                            $check_stmt->execute([$num, $property_id, $room_id]); // <<-- เพิ่ม room_id เข้าไป
                            $is_duplicate = $check_stmt->fetchColumn();
                            if ($is_duplicate > 0) {
                                return "ข้อมูลหมายเลขห้องพัก '" . htmlspecialchars($num) . "' ซ้ำกับห้องอื่น";
                            } else {
                                $stmt = $this->conn->prepare("UPDATE room SET Room_number=?, Room_status=?, Room_price=?, Room_capacity=?, Room_utensils=? WHERE Room_id=?");
                                $stmt->execute([$num, $status, $price, $cap, $uten, $room_id]);
                            }
                        } else {
                            $check_sql = "SELECT COUNT(*) FROM room WHERE Room_number = ? AND Property_id = ?";
                            $check_stmt = $this->conn->prepare($check_sql);
                            $check_stmt->execute([$num, $property_id]);
                            $is_duplicate = $check_stmt->fetchColumn();
                            if ($is_duplicate > 0) {
                                return "ข้อมูลหมายเลขห้องพัก '" . htmlspecialchars($num) . "' มีอยู่แล้ว";
                            } else {
                                $stmt = $this->conn->prepare("INSERT INTO room (Room_number, Room_price, Room_capacity, Room_utensils, Property_id, Room_status) VALUES (?, ?, ?, ?, ?, ?)");
                                $stmt->execute([$num, $price, $cap, $uten, $property_id, $status]);
                            }
                        }
                    }
                    return true;
                } else {
                    return "สถานะเจ้าของบ้านไม่ถูกต้อง.";
                }
            } catch (PDOException $e) {
                return $e->getMessage();
            }
        }
    }
}
