<?php

class Refund
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล

    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    public function get_listRefund()
    {
        $sql = "SELECT r.Refund_date,r.Refund_amount,r.Refund_reason,r.Host_Check,u.Firstname,u.Lastname,b.Booking_id,p.Property_name 
        FROM refund r 
        INNER JOIN booking b ON b.Booking_id = r.Booking_id 
        INNER JOIN user u ON b.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id WHERE r.Host_Check = 'pending' ORDER BY r.Refund_date ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $refund = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $refund;
    }


    public function get_listComplete()
    {
        $sql = "SELECT r.Refund_date,r.Refund_amount,r.Refund_reason,r.Host_Check,u.Firstname,u.Lastname,b.Booking_id,p.Property_name 
        FROM refund r 
        INNER JOIN booking b ON b.Booking_id = r.Booking_id 
        INNER JOIN user u ON b.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id WHERE r.Host_Check = 'approve' ORDER BY r.Refund_date ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $complete = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $complete;
    }
    public function get_listFailed()
    {
        $sql = "SELECT r.Refund_date,r.Refund_amount,r.Refund_reason,r.Host_Check,u.Firstname,u.Lastname,b.Booking_id,p.Property_name 
        FROM refund r 
        INNER JOIN booking b ON b.Booking_id = r.Booking_id 
        INNER JOIN user u ON b.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id WHERE r.Host_Check = 'unapprove' ORDER BY r.Refund_date ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $complete = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $complete;
    }

    public function submit_refund($email, $booking_id, $reason, $amount)
    {
        if (!$booking_id || !$reason || !$amount) {
            return "ข้อมูลไม่ครบถ้วน!";
        }
        $user_id = $this->get_id($email);
        if (is_string($user_id)) {
            return $user_id;
        }
        $check_refund = $this->check_refund($booking_id, $user_id);
        if ($check_refund > 0) {
            return "คุณได้ทำการขอคืนเงินสำหรับการจองนี้แล้ว";
        }
        $insertSQL = "INSERT INTO refund (User_id,Booking_id, Refund_reason) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($insertSQL);
        try {
            $stmt->execute([$user_id, $booking_id, $reason]);
            $refund_id = $this->conn->lastInsertId();
            $sql = "SELECT  DATEDIFF(b.Check_in,r.Refund_date) AS nights 
        FROM booking b INNER JOIN refund r ON b.Booking_id = r.Booking_id WHERE b.Booking_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$booking_id]);
            $nights = $stmt->fetch(PDO::FETCH_ASSOC);

            $percen = $this->Check_policy($nights);
            if ($percen == 0) {
                return "ไม่สามารถขอคืนเงินได้เนื่องจากน้อยกว่าระยะเวลาที่กำหนด";
            }
            $re_policy_id = $this->percen($percen);
            // $policen_id = $this->percen($percen);
            // $percen_amount = $amount * ($percen / 100);
            if (($re_policy_id) == null || $amount <= 0) {
                return "ไม่พบข้อมูลนโยบายการคืนเงิน";
            }
            $stmt = $this->conn->prepare("UPDATE refund SET Refund_amount = ?,Re_policy_id=? WHERE Refund_id = ?");
            $stmt->execute([$amount, $re_policy_id, $refund_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการส่งคำขอคืนเงิน: " . $e->getMessage();
        }
    }
    public function check_refund($booking_id, $user_id)
    {
        $sql = "SELECT COUNT(*) FROM refund WHERE Booking_id = ? AND User_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$booking_id, $user_id]);
        $refund = $stmt->fetchColumn();
        return $refund;
    }
    public function percen($percen)
    {
        $sql = "SELECT Re_Policy_id FROM refund_policy WHERE Refund_percen = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$percen]);
        $policy = $stmt->fetch(PDO::FETCH_ASSOC);
        return $policy['Re_Policy_id'];
    }
    public function get_id($email)
    {
        $sql = "SELECT User_id FROM user WHERE User_email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return  "ไม่พบผู้ใช้";
        }
        return $user['User_id'];
    }
    public function Check_policy($nights)
    {
        $sql = "SELECT Before_checkin,Refund_percen FROM refund_policy ORDER BY Before_checkin DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $policy = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($policy as $row) {
            if ($nights >= $row['Before_checkin']) {
                return $row['Refund_percen'];
            }
        }
        return 0;
    }

    public function approve_refund($refund_id)
    {
        if (!$refund_id) {
            return "ข้อมูลไม่ครบถ้วน!!!";
        }
        $updateSQL = "UPDATE refund SET Host_Check = 'approve' WHERE Booking_id = ?";
        $stmt = $this->conn->prepare($updateSQL);
        try {
            $stmt->execute([$refund_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการอนุมัติคำขอคืนเงิน: " . $e->getMessage();
        }
    }

    public function reject_refund($refund_id)
    {
        if (!$refund_id) {
            return "ข้อมูลไม่ครบถ้วน!";
        }
        $updateSQL = "UPDATE refund SET Host_Check = 'cancel' WHERE Refund_id = ?";
        $stmt = $this->conn->prepare($updateSQL);
        try {
            $stmt->execute([$refund_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการปฏิเสธคำขอคืนเงิน: " . $e->getMessage();
        }
    }
    public function get_verify_refund()
    {
        $sql = "SELECT r.Refund_id,b.Booking_id,b.Charge_id,p.Property_name,
        u.Firstname,u.Lastname,h.Host_firstname,h.Host_lastname,
        r.Refund_amount,r.Refund_reason,r.Refund_status,r.Refund_date 
        FROM refund r 
        INNER JOIN booking b ON r.Booking_id = b.Booking_id
        INNER JOIN user u ON r.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id
        INNER JOIN host h ON p.Host_id = h.Host_id
         WHERE r.Host_Check = 'approve' AND r.Refund_status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $refunds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $refunds;
    }
    public function get_complete_refund()
    {
        $sql = "SELECT r.Refund_id,b.Booking_id,b.Charge_id,p.Property_name,
        u.Firstname,u.Lastname,h.Host_firstname,h.Host_lastname,
        r.Refund_amount,r.Refund_reason,r.Refund_status,r.Refund_date 
        FROM refund r 
        INNER JOIN booking b ON r.Booking_id = b.Booking_id
        INNER JOIN user u ON r.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id
        INNER JOIN host h ON p.Host_id = h.Host_id
         WHERE r.Host_Check = 'approve' AND r.Refund_status = 'approve'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $refunds = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $refunds;
    }

    public function cancel_booking($booking_id)
    {
        if (!$booking_id) {
            return "ข้อมูลไม่ครบถ้วน!";
        }
        $updateSQL = "UPDATE booking SET Booking_status = 'failed' WHERE Booking_id = ?";
        $stmt = $this->conn->prepare($updateSQL);
        try {
            $stmt->execute([$booking_id]);
            return true;
        } catch (PDOException $e) {
            return "เกิดข้อผิดพลาดในการยกเลิกการจอง: " . $e->getMessage();
        }
    }
}
