<?php
class Host
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล

    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    // Method สำหรับการลงทะเบียน
    public function register($email, $firstname, $lastname, $Id_card, $phone, $password, $confirm_password)
    {
        if (empty($email) || empty($Id_card) || empty($firstname) || empty($lastname) || empty($phone) || empty($password) || empty($confirm_password)) {
            return "กรุณากรอกข้อมูลให้ครบ.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "รูปแบบอีเมลไม่ถูกต้อง.";
        }
        if ($password !== $confirm_password) {
            return "รหัสผ่านไม่ตรงกัน.";
        }
        // ตรวจสอบว่าอีเมลซ้ำหรือไม่
        $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM host WHERE Host_email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetchColumn() > 0) {
            return "อีเมลนี้ถูกใช้งานแล้ว";
        }
        // เข้ารหัสผ่าน
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // บันทึกข้อมูลลงฐานข้อมูล
        $stmt = $this->conn->prepare(
            "INSERT INTO host(Host_email,Host_IdCard, Host_firstname, Host_lastname, Host_phone, Host_password) 
             VALUES (?, ? ,?, ?, ?, ?)"
        );
        if ($stmt->execute([$email, $Id_card, $firstname, $lastname, $phone, $hashedPassword])) {
            return true; // ลงทะเบียนสำเร็จ
        } else {
            return "เกิดข้อผิดพลาดในการลงทะเบียน";
        }
    }

    // Method สำหรับการเข้าสู่ระบบ
    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_email = ?");
        $stmt->execute([$email]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);

        // ตรวจสอบว่าเจอผู้ใช้ และรหัสผ่านถูกต้องหรือไม่
        if ($host && password_verify($password, $host['Host_password'])) {
            // สร้าง Session
            $_SESSION['Host_email'] = $host['Host_email'];
            $_SESSION['Host_firstname'] = $host['Host_firstname'];
            return true; // ล็อกอินสำเร็จ
        } else {
            return "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
        }
    }

    public function updateProfile($email, $firstname, $lastname, $phone, $currentPassword, $newPassword, $confirm_password)
    {
        if (!$email || !$firstname || !$lastname || !$phone) {
            return "กรุณากรอกข้อมูล";
        }
        try {
            $stmt = $this->conn->prepare("SELECT Host_password FROM host WHERE Host_email = ?");
            $stmt->execute([$email]);
            $host = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$host) {
                return "ไม่พบข้อมูลบัญชีผู้ใช้";
            }
            $updateSQL = "UPDATE host SET Host_firstname = ?, Host_lastname = ?, Host_phone = ? WHERE Host_email = ?";
            $stmt = $this->conn->prepare($updateSQL);
            $stmt->execute([$firstname, $lastname, $phone, $email]);

            // หากผู้ใช้กรอกรหัสผ่านปัจจุบันและต้องการเปลี่ยนรหัสผ่าน
            if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if (!password_verify($currentPassword, $host['Host_password'])) {
                    $error = "รหัสผ่านปัจจุบันไม่ถูกต้อง";
                    return $error;
                } elseif ($newPassword !== $confirmPassword) {
                    return "รหัสผ่านใหม่ไม่ตรงกัน";
                } else {
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $this->conn->prepare("UPDATE host SET Host_password = ? WHERE Host_email = ?");
                    $stmt->execute([$hashedPassword, $email]);
                };
                return "success";
            }

            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function getDataHost($email)
    {
        try {
            $get = "
        SELECT *
        FROM host 
        WHERE Host_email = ?
    ";
            $stmt = $this->conn->prepare($get);
            $stmt->execute([$email]);
            $hosts = $stmt->fetch(PDO::FETCH_ASSOC);
            return $hosts;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
