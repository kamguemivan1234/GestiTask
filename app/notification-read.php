<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    include "Model/Notification.php";

    if (isset($_GET['notification_id'])) {
        $notification_id = intval($_GET['notification_id']);
        $result = notification_make_read($conn, $_SESSION['id'], $notification_id);
        
        if ($result) {
            header("Location: ../index.php?notification_read=success");
        } else {
            header("Location: ../index.php?error=notification_update_failed");
        }
        exit();
    } else {
        header("Location: ../index.php");
        exit();
    }
} else { 
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}
?>