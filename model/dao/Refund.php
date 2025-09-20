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
        $sql = "SELECT r.Refund_date,r.Refund_amount,r.Refund_reason,r.Refund_status,u.Firstname,u.Lastname,b.Booking_id,p.Property_name 
        FROM refund r 
        INNER JOIN booking b ON b.Booking_id = r.Booking_id 
        INNER JOIN user u ON b.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id WHERE r.Refund_status = 'pending' ORDER BY r.Refund_date ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $refund = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $refund;
    }

    public function get_listComplete()
    {
        $sql = "SELECT r.Refund_date,r.Refund_amount,r.Refund_reason,r.Refund_status,u.Firstname,u.Lastname,b.Booking_id,p.Property_name 
        FROM refund r 
        INNER JOIN booking b ON b.Booking_id = r.Booking_id 
        INNER JOIN user u ON b.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id WHERE r.Refund_status = 'approve' ORDER BY r.Refund_date ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $complete = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $complete;
    }
    public function get_listFailed()
    {
        $sql = "SELECT r.Refund_date,r.Refund_amount,r.Refund_reason,r.Refund_status,u.Firstname,u.Lastname,b.Booking_id,p.Property_name 
        FROM refund r 
        INNER JOIN booking b ON b.Booking_id = r.Booking_id 
        INNER JOIN user u ON b.User_id = u.User_id
        INNER JOIN property p ON b.Property_id = p.Property_id WHERE r.Refund_status = 'unapprove' ORDER BY r.Refund_date ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $complete = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $complete;
    }
}