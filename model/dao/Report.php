<?php
class Report
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล
    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function action_taken($review_id)
    {
        $sql = "UPDATE review_reports SET Report_status = 'action_taken' WHERE Review_id = ?";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$review_id]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function action_nothing($review_id)
    {
        $sql = "UPDATE review_reports SET Report_status = 'dismissed' WHERE Review_id = ?";
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([$review_id]);
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function get_violation()
    {
        $sql = "SELECT rp.Report_id,rv.Property_id,rp.Review_id,rp.Report_reason,
        rp.Create_at,o.User_email AS Reported_user, 
        CASE WHEN rp.Host_id IS NULL THEN rp.User_id 
        WHEN rp.User_id IS NULL THEN rp.Host_id 
        ELSE NULL  
        END AS Reported_by 
        FROM review_reports rp 
        INNER JOIN review rv ON rp.Review_id = rv.Review_id 
        INNER JOIN property p ON rv.Property_id = p.Property_id 
        INNER JOIN user o ON rv.User_id = o.User_id 
        WHERE rp.Report_status = 'pending';";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([]);
        $violation = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $violation;
    }
}
