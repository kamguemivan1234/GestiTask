<?php 
session_start();
header('Content-Type: application/json');

try {
    if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
        include "../DB_connection.php";
        include "Model/Notification.php";

        if (isset($_POST['notification_id'])) {
            $notification_id = intval($_POST['notification_id']);
            
            // Marquer la notification comme lue
            $result = notification_make_read($conn, $_SESSION['id'], $notification_id);
            
            if ($result) {
                // Compter les notifications non lues restantes
                $remaining_count = count_notification($conn, $_SESSION['id']);
                echo json_encode([
                    'success' => true, 
                    'message' => 'Notification marked as read',
                    'remaining_count' => $remaining_count
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update notification or notification not found']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No notification ID provided']);
        }
    } else { 
        echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>