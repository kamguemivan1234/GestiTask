<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "../DB_connection.php";
    include "Model/Message.php";
    include "Model/User.php";
    
    $receiver_id = intval($_POST['receiver_id'] ?? 0);
    $message = trim($_POST['message'] ?? '');
    
    if ($receiver_id <= 0 || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit();
    }
    
    // Vérifier que le destinataire existe
    $receiver = get_user_by_id($conn, $receiver_id);
    if (!$receiver) {
        echo json_encode(['success' => false, 'message' => 'Receiver not found']);
        exit();
    }
    
    // Vérifier les permissions (admin peut écrire à tous, employé seulement aux admins)
    if ($_SESSION['role'] == 'employee' && $receiver['role'] != 'admin') {
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        exit();
    }
    
    if (send_message($conn, $_SESSION['id'], $receiver_id, $message)) {
        echo json_encode(['success' => true, 'message' => 'Message sent']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>