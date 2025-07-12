<?php
// app/notification-delete-ajax.php - NOUVEAU FICHIER
session_start();
header('Content-Type: application/json');

try {
    if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
        include "../DB_connection.php";
        include "Model/Notification.php";

        if (isset($_POST['notification_id'])) {
            $notification_id = intval($_POST['notification_id']);
            
            // Récupérer les détails avant suppression pour le timestamp
            $notification_details = get_notification_details($conn, $notification_id, $_SESSION['id']);
            
            if ($notification_details) {
                // Supprimer la notification (soft delete)
                $result = delete_notification($conn, $_SESSION['id'], $notification_id);
                
                if ($result) {
                    // Compter les notifications restantes
                    $remaining_count = count_notification($conn, $_SESSION['id']);
                    
                    // Timestamp de suppression
                    $deletion_time = date('d/m/Y à H:i:s');
                    
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Notification supprimée avec succès',
                        'remaining_count' => $remaining_count,
                        'deletion_time' => $deletion_time,
                        'notification_message' => $notification_details['message']
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Impossible de supprimer la notification']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Notification non trouvée']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de notification manquant']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non authentifié']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>