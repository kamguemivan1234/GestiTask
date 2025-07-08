<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    include "Model/Notification.php";

    $count_notification = count_notification($conn, $_SESSION['id']);
    if ($count_notification && $count_notification > 0) {
        echo $count_notification;
    } else {
        echo "0";
    }
 }else{ 
  echo "0";
}
?>