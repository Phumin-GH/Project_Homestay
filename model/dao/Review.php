<?php
class Review
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล
    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function get_Reviews($property_id)
    {
        if (!$property_id) {
            return "ไม่พบข้อมูลบ้านพัก!";
        }
        $sql = "SELECT u.User_email,r.Rating,r.Comment,r.Create_at FROM review r 
        INNER JOIN user u ON r.User_id = u.User_id 
        INNER JOIN property p ON r.Property_id = p.Property_id
        WHERE r.Property_id =? ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $reviews;
    }

    public function addReview($user_email, $property_id, $rating, $comment)
    {
        if (!$user_email || !$property_id || !$rating || !$comment) {
            return "ข้อมูลไม่ครบถ้วน!";
        }
        $user_sql = "SELECT User_id FROM user WHERE User_email = ?";
        $stmt = $this->conn->prepare($user_sql);
        $stmt->execute([$user_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return  "ไม่พบผู้ใช้";
        }
        $insertSQL = "INSERT INTO review (User_id, Property_id, Rating, Comment) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($insertSQL);
        try {
            $stmt->execute([$user['User_id'], $property_id, $rating, $comment]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการส่งรีวิว: " . $e->getMessage();
        }
    }
}
