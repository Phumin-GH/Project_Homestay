<?php
if (session_start() != PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../model/config/db_connect.php';
require_once __DIR__ . '/../model/dao/Host.php';
require_once __DIR__ . '/../model/dao/User.php';
$HostmanageHandler = new Host($conn);
$UsermanageHandler = new User($conn);
if (isset($_SESSION['Admin_email'])) {
    if (isset($_POST['host_id'])) {
        $host_id = $_POST['host_id'];
        // $firstname = $_POST['Firstname'];
        // $lastname = $_POST['Lastname'];
        // $email = $_POST['Email'];
        // $phone = $_POST['Phone'];
        if (isset($_POST['approve_host'])) {
            $approved = 'active';
            $result = $HostmanageHandler->approve_host($approved, $host_id);
        } elseif (isset($_POST['cancel_host'])) {
            $cancel = 'cancel';
            $result = $HostmanageHandler->cancel_host($cancel, $host_id);
        } elseif (isset($_POST['rej_host'])) {
            $reject = 'active';
            $result = $HostmanageHandler->reject_host($reject, $host_id);
        } elseif (isset($_POST['edit_host'])) {
            $result = $HostmanageHandler->edit_host($firstname, $lastname, $email, $phone, $host_id);
        } elseif (isset($_POST['ban_host'])) {
            $baned = 'banned';
            $result = $HostmanageHandler->ban_host($baned, $host_id);
        }
        header("Location: ../views/admin/manage-hosts.php");
        exit();
    }
    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        // $firstname = $_POST['Firstname'];
        // $lastname = $_POST['Lastname'];
        // $email = $_POST['Email'];
        // $phone = $_POST['Phone'];

        if (isset($_POST['rej_host'])) {
            $reject = 'active';
            $result = $manageHandler->reject_host($reject, $host_id);
        } elseif (isset($_POST['del_host'])) {
            $result = $manageHandler->delete_host($host_id);
        } elseif (isset($_POST['edit_host'])) {
            $result = $manageHandler->edit_host($firstname, $lastname, $email, $phone, $host_id);
        } elseif (isset($_POST['ban_host'])) {
            $baned = 'ban';
            $result = $manageHandler->ban_host($baned, $host_id);
        }
        header("Location: ../views/admin/manage-hosts.php");
        exit();
    }
}
