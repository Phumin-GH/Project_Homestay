<?php
class Verify
{
    private $conn;
    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }
    public function get_verify_hosts()
    {
        $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_Status = 'pending_verify'");
        $stmt->execute();
        $verify_host = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $verify_host;
    }
    public function get_ban_hosts()
    {
        $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_Status = 'banned'");
        $stmt->execute();
        $ban_host = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ban_host;
    }
    public function get_cancel_hosts()
    {
        $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_Status = 'banned'");
        $stmt->execute();
        $cancel_host = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $cancel_host;
    }
    public function get_hosts()
    {
        $stmt = $this->conn->prepare("SELECT * FROM host WHERE Host_Status = 'active'");
        $stmt->execute();
        $hosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $hosts;
    }
    public function get_admins($email)
    {
        $stmt = $this->conn->prepare("SELECT * FROM admin_sys WHERE Admin_email = ?");
        $stmt->execute([$email]);
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $admins;
    }
    public function get_users()
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE User_Status = 'active'");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
    public function get_ban_user()
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE User_Status = 'banned'");
        $stmt->execute();
        $ban_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $ban_user;
    }
    public function get_inactive_user()
    {
        $stmt = $this->conn->prepare("SELECT * FROM user WHERE User_Status = 'inactive'");
        $stmt->execute();
        $inactive_user = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $inactive_user;
    }
    public function get_homestay()
    {
        $stmt = $this->conn->prepare("SELECT * FROM Property INNER JOIN Host on Property.Host_id = Host.Host_id WHERE Property.Property_status = 0");
        $stmt->execute();
        $homestay = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $homestay;
    }
    public function approve_property($property_id)
    {
        $status = 1;
        $updateSQL = "UPDATE property SET Property_status=? WHERE Property_id = ?";
        $stmt = $this->conn->prepare($updateSQL);
        $stmt->execute([$status, $property_id]);
        return true;
    }
    public function cancel_property($property_id)
    {
        $status = 2;
        $updateSQL = "UPDATE property SET Property_status=? WHERE Property_id = ?";
        $stmt = $this->conn->prepare($updateSQL);
        $stmt->execute([$status, $property_id]);
        return true;
    }
}
