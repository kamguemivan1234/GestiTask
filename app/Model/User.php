<?php

function get_all_users($conn) {
    try {
        $sql = "SELECT * FROM users ORDER BY full_name ASC";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error in get_all_users: " . $e->getMessage());
        return [];
    }
}

function get_users_by_role($conn, $role){
    $sql = "SELECT * FROM users WHERE role = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$role]);

    if($stmt->rowCount() > 0){
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }else $users = [];

    return $users;
}

function insert_user($conn, $data){
    $sql = "INSERT INTO users (full_name, username, password, role) VALUES(?,?,?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

function update_user($conn, $data){
    $sql = "UPDATE users SET full_name=?, username=?, password=?, role=? WHERE id=? AND role=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

function delete_user($conn, $data) {
    // Extraire les données
    $userId = $data[0];
    $role = $data[1];

    // Supprimer d'abord les messages de cet utilisateur
    $deleteMessagesSql = "DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?";
    $stmtMessages = $conn->prepare($deleteMessagesSql);
    $stmtMessages->execute([$userId, $userId]);

    // Supprimer d'abord les tâches assignées à cet utilisateur
    $deleteTasksSql = "DELETE FROM tasks WHERE assigned_to = ?";
    $stmtTasks = $conn->prepare($deleteTasksSql);
    $stmtTasks->execute([$userId]);

    // Ensuite, supprimer l'utilisateur
    $deleteUserSql = "DELETE FROM users WHERE id = ? AND role = ?";
    $stmtUser = $conn->prepare($deleteUserSql);
    $stmtUser->execute($data);
}

function get_user_by_id($conn, $id){
    try {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        
        if($stmt->rowCount() > 0){
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }else $user = false;

        return $user;
    } catch (Exception $e) {
        error_log("Error in get_user_by_id: " . $e->getMessage());
        return false;
    }
}

function update_profile($conn, $data){
    $sql = "UPDATE users SET full_name=?, password=? WHERE id=? ";
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
}

function count_users($conn){
    $sql = "SELECT id FROM users WHERE role='employee'";
    $stmt = $conn->prepare($sql);
    $stmt->execute([]);

    return $stmt->rowCount();
}

?>