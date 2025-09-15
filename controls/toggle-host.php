<?php 
if(session_start() != PHP_SESSION_ACTIVE){
    session_start();
}
include '../config/db_connect.php'; 
if(isset($_SESSION['Admin_email']) && isset($_POST['host_id'])){
    $host_id = $_POST['host_id'];
    $firstname = $_POST['Firstname'];
    $lastname = $_POST['Lastname'];
    $email = $_POST['Email'];
    $phone = $_POST['Phone'];

    if(isset($_POST['approve_host'])){
        $approved = 'active';
        $sql = "UPDATE host SET Host_status = ? WHERE Host_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute( [$approved,$host_id]);
    }elseif(isset($_POST['cancel_host'])){
        $cancel = 'cancel';
        $sql = "UPDATE host SET Host_status = ? WHERE Host_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute( [$cancel,$host_id]);
    }elseif(isset($_POST['rej_host'])){
        $reject = 'active';
        $sql = "UPDATE host SET Host_status = ? WHERE Host_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute( [$reject,$host_id]);
    }elseif(isset($_POST['del_host'])){
        
        $sql = "DELETE FROM  host  WHERE Host_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute( [$host_id]);
    }
    elseif(isset($_POST['edit_host'])){
        
        $sql = "UPDATE host SET Host_firstname = ? , Host_lastname = ?, Host_email = ? , Host_phone = ? WHERE Host_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute( [$firstname, $lastname, $email, $phone, $host_id]);
    }
    header("Location: ../admin/manage-hosts.php");
    exit();
    
}