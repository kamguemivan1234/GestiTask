<?php
session_start();
header('Content-Type: application/json');

// Logging pour debug
error_log("=== GET-MESSAGES DEBUG ===");
error_log("Session ID: " . ($_SESSION['id'] ?? 'NOT SET'));
error_log("Session Role: " . ($_SESSION['role'] ?? 'NOT SET'));
error_log("Contact ID requested: " . ($_GET['contact_id'] ?? 'NOT SET'));

try {
    if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
        error_log("Authentication failed");
        echo json_encode(['success' => false, 'message' => 'Non authentifié']);
        exit();
    }

    include "../DB_connection.php";
    include "Model/Message.php";

    $contact_id = intval($_GET['contact_id'] ?? 0);
    $current_user_id = $_SESSION['id'];

    if ($contact_id <= 0) {
        error_log("Invalid contact ID: " . $contact_id);
        echo json_encode(['success' => false, 'message' => 'ID de contact invalide']);
        exit();
    }

    error_log("Fetching messages between user $current_user_id and contact $contact_id");

    // Marquer les messages comme lus (messages reçus du contact)
    $marked = mark_messages_as_read($conn, $contact_id, $current_user_id);
    error_log("Messages marked as read: " . ($marked ? 'YES' : 'NO'));

    // Récupérer les messages avec une requête directe pour debug
    $sql = "SELECT m.*, s.full_name as sender_name, r.full_name as receiver_name 
            FROM messages m
            JOIN users s ON m.sender_id = s.id
            JOIN users r ON m.receiver_id = r.id
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?)
            ORDER BY m.created_at ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute([$current_user_id, $contact_id, $contact_id, $current_user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    error_log("SQL executed: " . $sql);
    error_log("Parameters: [$current_user_id, $contact_id, $contact_id, $current_user_id]");
    error_log("Messages found: " . count($messages));

    // Debug supplémentaire : vérifier s'il y a des messages dans la table
    $check_sql = "SELECT COUNT(*) as total FROM messages WHERE sender_id = ? OR receiver_id = ? OR sender_id = ? OR receiver_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([$current_user_id, $current_user_id, $contact_id, $contact_id]);
    $total_messages = $check_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    error_log("Total messages involving these users: " . $total_messages);

    if (isset($_GET['debug'])) {
        echo json_encode([
            'success' => true, 
            'messages' => $messages,
            'debug' => [
                'current_user_id' => $current_user_id,
                'contact_id' => $contact_id,
                'total_messages_in_db' => $total_messages,
                'sql' => $sql,
                'parameters' => [$current_user_id, $contact_id, $contact_id, $current_user_id]
            ]
        ]);
    } else {
        echo json_encode(['success' => true, 'messages' => $messages]);
    }

} catch (Exception $e) {
    error_log("Error in get-messages.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>