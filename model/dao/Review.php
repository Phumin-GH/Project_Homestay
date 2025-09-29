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
        $sql = "SELECT u.User_email,r.Review_id,r.Rating,r.Comment,r.Create_at FROM review r 
        INNER JOIN user u ON r.User_id = u.User_id 
        INNER JOIN property p ON r.Property_id = p.Property_id
        WHERE r.Property_id =? AND r.Review_status = 'normal'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$property_id]);
        $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $reviews;
    }
    public function get_violation()
    {
        $sql = "SELECT rp.Report_id,rp.Review_id,reporter.User_email AS Reported_by,owner.User_email AS Reported_user,rp.Report_reason,rp.Create_at
        FROM review_reports rp
        INNER JOIN review rv ON rp.Review_id = rv.Review_id
        INNER JOIN user owner ON  rv.User_id = owner.User_id
        INNER JOIN user reporter ON  rp.User_id = reporter.User_id
        WHERE rp.Report_status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([]);
        $violation = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $violation;
    }
    public function get_user_id($user_email)
    {
        $sql = "SELECT User_id FROM user WHERE User_email=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_email]);
        $user_id = $stmt->fetchColumn();
        return $user_id;
    }
    public function get_host_id($host_email)
    {
        $sql = "SELECT Host_id FROM host WHERE Host_email=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$host_email]);
        $host_id = $stmt->fetchColumn();
        return $host_id;
    }
    public function violation($review_id, $user_email, $host_email, $reason)
    {
        $host_id = null;
        $user_id = null;
        if (!empty($user_email) && empty($host_email)) {
            $user_id = $this->get_user_id($user_email);
            $result = $this->check_report_user($user_id, $review_id);
            if ($result > 0) {
                return "ไม่สามารถดำเนินการซ้ำได้";
            }
        } elseif (!empty($host_email) && empty($user_email)) {
            $host_id = $this->get_host_id($host_email);
            $result = $this->check_report_host($host_id, $review_id);
            if ($result > 0) {
                return "ไม่สามารถดำเนินการซ้ำได้";
            }
        }
        $sql = "INSERT INTO review_reports (Review_id,User_id,Host_id,Report_reason) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$review_id, $user_id, $host_id, $reason]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function check_report_user($user_id, $review_id)
    {
        $sql = "SELECT COUNT(*) FROM review_reports WHERE  User_id=? AND Review_id = ?";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$user_id, $review_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function check_report_host($host_id, $review_id)
    {
        $sql = "SELECT COUNT(*) FROM review_reports WHERE Host_id =? AND Review_id = ?";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$host_id, $review_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function deleteReview($review_id)
    {
        $sql = "DELETE FROM review WHERE Review_id = ?";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$review_id]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
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