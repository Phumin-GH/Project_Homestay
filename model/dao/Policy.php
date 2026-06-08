<?php
class Policy
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล

    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    /**
     * ดึงข้อมูลนโยบายทั้งหมดหรือตาม ID
     */
    public function get_Policy($policy_id = null)
    {
        if ($policy_id) {
            $sql = "SELECT * FROM refund_policy WHERE Re_Policy_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$policy_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT * FROM refund_policy ORDER BY Before_checkin DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * เพิ่มนโยบายใหม่
     */
    public function Add_Policy($before_checkin, $refund_percen, $description, $email)
    {
        // ตรวจสอบว่ามีนโยบายที่ซ้ำกันหรือไม่
        $checkSql = "SELECT COUNT(*) FROM refund_policy WHERE Before_checkin = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$before_checkin]);

        if ($checkStmt->fetchColumn() > 0) {
            return "มีนโยบายสำหรับจำนวนวันนี้อยู่แล้ว";
        }

        // ดึง Admin ID จาก email
        $sql = "SELECT Admin_id FROM admin_sys WHERE Admin_email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $admin_result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin_result) {
            return "ไม่พบข้อมูลผู้ดูแลระบบ";
        }

        $admin_id = $admin_result['Admin_id'];

        // เพิ่มนโยบายใหม่
        $sql = "INSERT INTO refund_policy (Before_checkin, Refund_percen, Policy_description, Admin_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([$before_checkin, $refund_percen, $description, $admin_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการเพิ่มนโยบาย: " . $e->getMessage();
        }
    }

    /**
     * แก้ไขนโยบาย
     */
    public function Update_Policy($policy_id, $before_checkin, $refund_percen, $description, $email)
    {
        // ตรวจสอบว่ามีนโยบายที่ซ้ำกันหรือไม่ (ยกเว้น ID ปัจจุบัน)
        $checkSql = "SELECT COUNT(*) FROM refund_policy WHERE Before_checkin = ? AND Re_Policy_id != ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$before_checkin, $policy_id]);

        if ($checkStmt->fetchColumn() > 0) {
            return "มีนโยบายสำหรับจำนวนวันนี้อยู่แล้ว";
        }

        // ดึง Admin ID จาก email
        $sql = "SELECT Admin_id FROM admin_sys WHERE Admin_email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $admin_result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin_result) {
            return "ไม่พบข้อมูลผู้ดูแลระบบ";
        }

        $admin_id = $admin_result['Admin_id'];

        // แก้ไขนโยบาย
        $sql = "UPDATE refund_policy SET Before_checkin = ?, Refund_percen = ?, Policy_description = ?, Admin_id = ? WHERE Re_Policy_id = ?";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([$before_checkin, $refund_percen, $description, $admin_id, $policy_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการแก้ไขนโยบาย: " . $e->getMessage();
        }
    }

    /**
     * ลบนโยบาย
     */
    public function Delete_Policy($policy_id)
    {
        // ตรวจสอบว่ามีการใช้งานนโยบายนี้หรือไม่
        $checkSql = "SELECT COUNT(*) FROM refund WHERE Re_policy_id = ?";
        $checkStmt = $this->conn->prepare($checkSql);
        $checkStmt->execute([$policy_id]);

        if ($checkStmt->fetchColumn() > 0) {
            return "ไม่สามารถลบนโยบายที่มีการใช้งานแล้ว";
        }

        // ลบนโยบาย
        $sql = "DELETE FROM refund_policy WHERE Re_Policy_id = ?";
        $stmt = $this->conn->prepare($sql);

        try {
            $stmt->execute([$policy_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการลบนโยบาย: " . $e->getMessage();
        }
    }

    /**
     * ดึงสถิติการใช้งานนโยบาย
     */
    public function get_Policy_Stats()
    {
        $stats = [];

        // จำนวนนโยบายทั้งหมด
        $sql = "SELECT COUNT(*) as total FROM refund_policy";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['total_policies'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // นโยบายที่ใช้บ่อยที่สุด
        $sql = "SELECT rp.Before_checkin, rp.Refund_percen, COUNT(r.Re_policy_id) as usage_count
                FROM refund_policy rp
                LEFT JOIN refund r ON rp.Re_Policy_id = r.Re_policy_id
                GROUP BY rp.Re_Policy_id, rp.Before_checkin, rp.Refund_percen
                ORDER BY usage_count DESC
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $mostUsed = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mostUsed && $mostUsed['usage_count'] > 0) {
            $stats['most_used_policy'] = $mostUsed['Before_checkin'] . ' วัน (' . $mostUsed['Refund_percen'] . '%)';
            $stats['most_used_count'] = $mostUsed['usage_count'];
        } else {
            $stats['most_used_policy'] = 'ไม่มีการใช้งาน';
            $stats['most_used_count'] = 0;
        }

        // การใช้งานรวมทั้งหมด
        $sql = "SELECT COUNT(*) as total FROM refund WHERE Re_policy_id IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $stats['total_usage'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        return $stats;
    }
}