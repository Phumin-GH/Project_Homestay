<?php
class Forgot_Password
{
    private $conn;
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function forgot_pwd_User($Email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email=?");
        $stmt->execute([$Email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return "ไม่พบข้อมูลผู้ใช้";
        }
        // สร้าง token
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        $stmt = $this->conn->prepare("SELECT COUNT(Expires_at) FROM user WHERE User_id = ?");
        $stmt->execute([$user['User_id']]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $stmt = $this->conn->prepare("UPDATE user SET  Expires_at = null,Token=null WHERE User_id = ?");
            $stmt->execute([$user['User_id']]);
        }
        $stmt = $this->conn->prepare("UPDATE user SET Token =?, Expires_at = ? WHERE User_id = ?");
        try {
            $stmt->execute([$token, $expires, $user['User_id']]);
            $sql = "SELECT Token FROM user WHERE User_id =?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$user['User_id']]);
            $token = $stmt->fetchColumn();
            return $token;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
    public function forgot_pwd_Host($Email)
    {
        $Host_email = $_POST['Host_email'] ?? '';
        $stmt = $this->conn->prepare("SELECT Host_id FROM host WHERE Host_email=?");
        $stmt->execute([$Email]);
        $host = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$host) {
            return "ไม่พบข้อมูลเจ้าของบ้าน";
        }
        // สร้าง token
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        $stmt = $this->conn->prepare("SELECT COUNT(Expires_at) FROM host WHERE Host_id = ?");
        $stmt->execute([$host['Host_id']]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $stmt = $this->conn->prepare("UPDATE host SET  Expires_at = null,Token=null WHERE Host_id = ?");
            $stmt->execute([$host['Host_id']]);
        }
        $stmt = $this->conn->prepare("UPDATE host SET Token =?, Expires_at = ? WHERE Host_id = ?");
        try {
            $stmt->execute([$token, $expires, $host['Host_id']]);
            // $sql = "SELECT Token FROM user WHERE User_id =?";
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}