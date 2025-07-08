<?php

function send_message($conn, $sender_id, $receiver_id, $message) {
    try {
        $sql = "INSERT INTO messages (sender_id, receiver_id, message, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        return $stmt->execute([$sender_id, $receiver_id, $message]);
    } catch (Exception $e) {
        error_log("Error in send_message: " . $e->getMessage());
        return false;
    }
}

function get_conversations($conn, $user_id) {
    try {
        // Vérifier que la session existe
        if (!isset($_SESSION['role'])) {
            error_log("Session role not set in get_conversations");
            return [];
        }
        
        if ($_SESSION['role'] == 'admin') {
            // Pour l'admin : obtenir TOUS les employés (même sans messages)
            $sql = "SELECT u.id, u.full_name, u.username,
                           COALESCE((SELECT message FROM messages 
                            WHERE (sender_id = u.id AND receiver_id = ?) 
                               OR (sender_id = ? AND receiver_id = u.id) 
                            ORDER BY created_at DESC LIMIT 1), '') as last_message,
                           (SELECT created_at FROM messages 
                            WHERE (sender_id = u.id AND receiver_id = ?) 
                               OR (sender_id = ? AND receiver_id = u.id) 
                            ORDER BY created_at DESC LIMIT 1) as last_message_time,
                           COALESCE((SELECT COUNT(*) FROM messages 
                            WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0), 0) as unread_count
                    FROM users u 
                    WHERE u.role = 'employee' AND u.id != ?
                    ORDER BY 
                        CASE WHEN last_message_time IS NULL THEN 1 ELSE 0 END,
                        last_message_time DESC, 
                        u.full_name ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
        } else {
            // Pour l'employé : obtenir TOUS les admins (même sans messages)
            $sql = "SELECT u.id, u.full_name, u.username,
                           COALESCE((SELECT message FROM messages 
                            WHERE (sender_id = u.id AND receiver_id = ?) 
                               OR (sender_id = ? AND receiver_id = u.id) 
                            ORDER BY created_at DESC LIMIT 1), '') as last_message,
                           (SELECT created_at FROM messages 
                            WHERE (sender_id = u.id AND receiver_id = ?) 
                               OR (sender_id = ? AND receiver_id = u.id) 
                            ORDER BY created_at DESC LIMIT 1) as last_message_time,
                           COALESCE((SELECT COUNT(*) FROM messages 
                            WHERE sender_id = u.id AND receiver_id = ? AND is_read = 0), 0) as unread_count
                    FROM users u 
                    WHERE u.role = 'admin' AND u.id != ?
                    ORDER BY 
                        CASE WHEN last_message_time IS NULL THEN 1 ELSE 0 END,
                        last_message_time DESC, 
                        u.full_name ASC";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
        }
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("get_conversations returned " . count($result) . " users for role " . $_SESSION['role']);
        return $result ?: [];
        
    } catch (Exception $e) {
        error_log("Error in get_conversations: " . $e->getMessage());
        return [];
    }
}

function get_messages_between_users($conn, $user1_id, $user2_id, $limit = 50) {
    try {
        error_log("Getting messages between user $user1_id and user $user2_id");
        
        $sql = "SELECT m.*, s.full_name as sender_name, r.full_name as receiver_name 
                FROM messages m
                JOIN users s ON m.sender_id = s.id
                JOIN users r ON m.receiver_id = r.id
                WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                   OR (m.sender_id = ? AND m.receiver_id = ?)
                ORDER BY m.created_at ASC
                LIMIT ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user1_id, $user2_id, $user2_id, $user1_id, $limit]);
        
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found " . count($result) . " messages between users $user1_id and $user2_id");
        
        // Debug : vérifier tous les messages de ces utilisateurs
        $debug_sql = "SELECT * FROM messages WHERE sender_id IN (?, ?) OR receiver_id IN (?, ?)";
        $debug_stmt = $conn->prepare($debug_sql);
        $debug_stmt->execute([$user1_id, $user2_id, $user1_id, $user2_id]);
        $debug_result = $debug_stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Debug: Total messages involving these users: " . count($debug_result));
        
        return $result ?: [];
        
    } catch (Exception $e) {
        error_log("Error in get_messages_between_users: " . $e->getMessage());
        return [];
    }
}

function mark_messages_as_read($conn, $sender_id, $receiver_id) {
    try {
        $sql = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?";
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute([$sender_id, $receiver_id]);
        error_log("Marked messages as read from $sender_id to $receiver_id. Affected rows: " . $stmt->rowCount());
        return $result;
    } catch (Exception $e) {
        error_log("Error in mark_messages_as_read: " . $e->getMessage());
        return false;
    }
}

function count_unread_messages($conn, $user_id) {
    try {
        $sql = "SELECT COUNT(*) as count FROM messages WHERE receiver_id = ? AND is_read = 0";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } catch (Exception $e) {
        error_log("Error in count_unread_messages: " . $e->getMessage());
        return 0;
    }
}

?>