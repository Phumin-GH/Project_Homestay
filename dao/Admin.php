<?php
class Admin
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล
    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    // Method สำหรับการเข้าสู่ระบบ
    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM admin_sys WHERE Admin_email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        // ตรวจสอบว่าเจอผู้ใช้ และรหัสผ่านถูกต้องหรือไม่
        if ($admin && password_verify($password, $admin['Admin_password'])) {
            // สร้าง Session
            $_SESSION['Admin_email'] = $admin['Admin_email'];
            return true; // ล็อกอินสำเร็จ
        } else {
            return "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
        }
    }
    public function updateProfile($email, $username, $phone, $currentPassword, $newPassword, $confirmPassword)
    {
        if (!$email || !$username || !$phone) {
            return "กรุณากรอกข้อมูล";
        }
        $stmt = $this->conn->prepare("SELECT Admin_password FROM admin_sys WHERE Admin_email = ?");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$admin) {
            return "ไม่พบข้อมูลบัญชีผู้ใช้";
        }
        $updateSQL = "UPDATE admin_sys SET Admin_username = ?, Admin_phone = ? WHERE Admin_email = ?";
        $stmt = $this->conn->prepare($updateSQL);
        $stmt->execute([$username, $phone, $email]);

        // หากผู้ใช้กรอกรหัสผ่านปัจจุบันและต้องการเปลี่ยนรหัสผ่าน
        if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
            if (!password_verify($currentPassword, $admin['Admin_password'])) {
                return "รหัสผ่านปัจจุบันไม่ถูกต้อง";
            } elseif ($newPassword !== $confirmPassword) {
                return "รหัสผ่านใหม่ไม่ตรงกัน";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("UPDATE admin SET Admin_password = ? WHERE Admin_email = ?");
                $stmt->execute([$hashedPassword, $email]);
            }
            return "success";
        }
        return true;
    }
    public function getDataAdmin($email)
    {
        $get = "
    SELECT *
    FROM admin_sys 
    WHERE Admin_email = ?
";
        $stmt = $this->conn->prepare($get);
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        return $admin;
    }
}
