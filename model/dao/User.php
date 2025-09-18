<?php
class User
{
    private $conn; // สำหรับเก็บการเชื่อมต่อฐานข้อมูล
    // รับการเชื่อมต่อ DB เข้ามาเมื่อ Class ถูกสร้าง
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    // Method สำหรับการลงทะเบียน
    public function register($email, $firstname, $lastname, $phone, $password)
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
        $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM user WHERE User_email = ?");
        $checkStmt->execute([$email]);
        if ($checkStmt->fetchColumn() > 0) {
            return "อีเมลนี้ถูกใช้งานแล้ว";
        }
        // เข้ารหัสผ่าน
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        // บันทึกข้อมูลลงฐานข้อมูล
        $stmt = $this->conn->prepare(
            "INSERT INTO user (User_email, Firstname, Lastname, Phone, User_password) 
             VALUES (?, ?, ?, ?, ?)"
        );
        if ($stmt->execute([$email, $firstname, $lastname, $phone, $hashedPassword])) {
            return true; // ลงทะเบียนสำเร็จ
        } else {
            return "เกิดข้อผิดพลาดในการลงทะเบียน";
        }
    }
    // Method สำหรับการเข้าสู่ระบบ
    public function login($email, $password)
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // ตรวจสอบว่าเจอผู้ใช้ และรหัสผ่านถูกต้องหรือไม่
        if ($user && password_verify($password, $user['User_password'])) {

            $_SESSION['User_email'] = $user['User_email'];
            $_SESSION['Firstname'] = $user['Firstname'];
            return true;
        } else {
            return "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
        }
    }
    public function updateProfile($email, $firstname, $lastname, $phone, $currentPassword, $newPassword, $confirm_password)
    {
        if (!$email || !$firstname || !$lastname || !$phone) {
            return "กรุณากรอกข้อมูล";
        }
        $stmt = $this->conn->prepare("SELECT User_password FROM user WHERE User_email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return "ไม่พบข้อมูลบัญชีผู้ใช้";
        }
        $updateSQL = "UPDATE user SET Firstname = ?, Lastname = ?, Phone = ? WHERE User_email = ?";
        $stmt = $this->conn->prepare($updateSQL);
        $stmt->execute([$firstname, $lastname, $phone, $email]);

        // หากผู้ใช้กรอกรหัสผ่านปัจจุบันและต้องการเปลี่ยนรหัสผ่าน
        if (!empty($currentPassword) && !empty($newPassword) && !empty($confirmPassword)) {
            if (!password_verify($currentPassword, $user['User_password'])) {
                return "รหัสผ่านปัจจุบันไม่ถูกต้อง";
            } elseif ($newPassword !== $confirmPassword) {
                return "รหัสผ่านใหม่ไม่ตรงกัน";
            } else {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = $this->conn->prepare("UPDATE user SET User_password = ? WHERE User_email = ?");
                $stmt->execute([$hashedPassword, $email]);
            }
            return "success";
        }
        return true;
    }
    public function getDataUser($email)
    {
        $get = "
    SELECT *
    FROM user 
    WHERE User_email = ?
";
        $stmt = $this->conn->prepare($get);
        $stmt->execute([$email]);
        $users = $stmt->fetch(PDO::FETCH_ASSOC);
        return $users;
    }
    public function delete_host($user_id)
    {
        $sql = "DELETE FROM  user  WHERE User_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return true;
    }
    public function edit_user($firstname, $lastname, $email, $phone, $user_id)
    {
        $sql = "UPDATE user SET Firstname = ? , Lastname = ?, User_email = ? , Phone = ? WHERE User_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$firstname, $lastname, $email, $phone, $user_id]);
        return true;
    }

    public function reject_user($reject, $user_id)
    {
        $sql = "UPDATE user SET User_status = ? WHERE User_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$reject, $user_id]);
        return true;
    }
    public function ban_user($baned, $user_id)
    {
        $sql = "UPDATE user SET User_status = ? WHERE User_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$baned, $user_id]);
        return true;
    }
}
