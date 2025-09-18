<?php
class Favorites
{
    private $conn;
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function show_Favorites($email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user_id = $stmt->fetchColumn();

        // ดึง property_id ของ favorites ของ user
        $stmt = $this->conn->prepare("SELECT property_id FROM favorite WHERE User_id = ?");
        $stmt->execute([$user_id]);
        $fav_btn = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $fav_btn;
    }
    // เพิ่ม/ลบ favorite แบบ toggle
    public function addFavorites($email, $property_id)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user_id = $stmt->fetchColumn();
        $check = $this->conn->prepare("SELECT 1 FROM favorite WHERE User_id = ? AND property_id = ?");
        $check->execute([$user_id, $property_id]);
        $exists = $check->fetchColumn();

        if ($exists) {
            $del = $this->conn->prepare("DELETE FROM favorite WHERE User_id = ? AND property_id = ?");
            $del->execute([$user_id, $property_id]);
            return "removed";
        } else {
            $insert = $this->conn->prepare("INSERT INTO favorite (User_id, property_id) VALUES (?, ?)");
            $insert->execute([$user_id, $property_id]);
            return "added";
        }
    }
    // ลบ favorite
    public function removeFavorites($email, $property_id)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user_id = $stmt->fetchColumn();
        $check = $this->conn->prepare("SELECT 1 FROM favorite WHERE User_id = ? AND property_id = ?");
        $check->execute([$user_id, $property_id]);
        $exists = $check->fetchColumn();

        if ($exists) {
            $del = $this->conn->prepare("DELETE FROM favorite WHERE User_id = ? AND property_id = ?");
            $del->execute([$user_id, $property_id]);

            return "ลบข้อมูลใน favorites";
        } else {
            return "ไม่พบข้อมูลใน favorites";
        }
    }
    public function get_listFavorites($email)
    {
        if (empty($email)) {
            return false;
        }
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user_id = $stmt->fetchColumn();
        $select = $this->conn->prepare("SELECT f.Favorite_id, h.Host_firstname, h.Host_lastname, h.Host_phone,p.Property_id, p.Property_image, p.Property_name, p.Property_province, p.Property_district, p.Property_subdistrict 
        FROM favorite f
        INNER JOIN property p ON f.Property_id = p.Property_id
        INNER JOIN host h ON p.Host_id = h.Host_id
        WHERE User_id = ?");
        $select->execute([$user_id]);
        $favorites = $select->fetchAll(PDO::FETCH_ASSOC);
        return $favorites;
    }
}
