<?php
class Forgot_Password
{
    private $conn;
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function forgot_password($User_email)
    {
        $stmt = $this->conn->prepare("SELECT User_id FROM user WHERE User_email=?");
        $stmt->execute([$User_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$user) {
            return false;
        }
        // สร้าง token
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+30 minutes"));
        $stmt = $this->conn->prepare("SELECT COUNT(Expires_at) FROM user WHERE User_id = ?");
        $stmt->execute([$user['User_id']]);
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            $stmt = $this->conn->prepare("UPDATE user SET  Expires_at = null WHERE User_id = ?");
            $stmt->execute([$user['User_id']]);
        }
        $stmt = $this->conn->prepare("UPDATE user SET Token =?, Expires_at = ? WHERE User_id = ?");
        $stmt->execute([$token, $expires, $user['User_id']]);
        return $user;
    }
}