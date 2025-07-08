<?php 
session_start();

// Définir la fonction timeAgo avant de l'utiliser
function timeAgo($datetime) {
    if (empty($datetime)) return '';
    
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . 'm';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . 'h';
    } elseif ($time < 604800) {
        $days = floor($time / 86400);
        return $days . 'd';
    } else {
        return date('M j', strtotime($datetime));
    }
}

if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Message.php";
    include "app/Model/User.php";
    
    // Debug: vérifier la session
    error_log("Chat.php - User ID: " . $_SESSION['id'] . ", Role: " . $_SESSION['role']);
    
    $conversations = get_conversations($conn, $_SESSION['id']);
    
    // Debug: vérifier le résultat
    error_log("Chat.php - Found " . count($conversations) . " conversations");
    
    if (empty($conversations)) {
        // Debug supplémentaire : vérifier combien d'utilisateurs existent
        $all_users = get_all_users($conn);
        error_log("Chat.php - Total users in database: " . count($all_users));
        
        foreach ($all_users as $user) {
            error_log("User: ID=" . $user['id'] . ", Role=" . $user['role'] . ", Name=" . $user['full_name']);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chat - Task Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .chat-container {
            display: flex;
            height: calc(100vh - 100px);
            background: #f8fafc;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }
        
        .chat-sidebar {
            width: 350px;
            background: white;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
        }
        
        .chat-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }
        
        .chat-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
        }
        
        .chat-header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .conversations-list {
            flex: 1;
            overflow-y: auto;
            padding: 0;
        }
        
        .conversation-item {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .conversation-item:hover {
            background: #f8fafc;
        }
        
        .conversation-item.active {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-right: 3px solid #667eea;
        }
        
        .conversation-info {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .conversation-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
        }
        
        .conversation-time {
            font-size: 12px;
            color: #a0aec0;
        }
        
        .conversation-preview {
            color: #718096;
            font-size: 13px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .unread-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 600;
        }
        
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: white;
        }
        
        .chat-main-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
            background: white;
        }
        
        .chat-contact-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .chat-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        
        .chat-contact-details h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
        }
        
        .chat-contact-details p {
            margin: 2px 0 0 0;
            font-size: 14px;
            color: #718096;
        }
        
        .messages-container {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f8fafc;
        }
        
        .message {
            margin-bottom: 16px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }
        
        .message.sent {
            flex-direction: row-reverse;
        }
        
        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
        }
        
        .message.received .message-bubble {
            background: white;
            color: #2d3748;
            border-bottom-left-radius: 6px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .message.sent .message-bubble {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-bottom-right-radius: 6px;
        }
        
        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 4px;
        }
        
        .message.received .message-time {
            color: #a0aec0;
        }
        
        .message.sent .message-time {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .chat-input-container {
            padding: 20px;
            background: white;
            border-top: 1px solid #e2e8f0;
        }
        
        .chat-input-form {
            display: flex;
            gap: 12px;
            align-items: flex-end;
        }
        
        .chat-input {
            flex: 1;
            border: 2px solid #e2e8f0;
            border-radius: 20px;
            padding: 12px 16px;
            font-size: 16px;
            resize: none;
            max-height: 100px;
            min-height: 44px;
            outline: none;
            transition: border-color 0.3s ease;
        }
        
        .chat-input:focus {
            border-color: #667eea;
        }
        
        .send-button {
            width: 44px;
            height: 44px;
            border: none;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .send-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
        
        .send-button:disabled {
            background: #cbd5e0;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .empty-chat {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #718096;
            text-align: center;
        }
        
        .empty-chat i {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        
        .empty-chat h3 {
            margin: 0 0 8px 0;
            color: #4a5568;
        }
        
        .empty-conversations {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #718096;
            text-align: center;
            padding: 40px 20px;
        }
        
        .empty-conversations i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            margin: 10px;
            border-radius: 5px;
            font-size: 12px;
            color: #856404;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .chat-container {
                margin: 10px;
                height: calc(100vh - 120px);
            }
            
            .chat-sidebar {
                width: 100%;
                display: none;
            }
            
            .chat-sidebar.mobile-show {
                display: flex;
            }
            
            .chat-main {
                width: 100%;
                display: none;
            }
            
            .chat-main.mobile-show {
                display: flex;
            }
        }
        
        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            .chat-container {
                background: #1a202c;
            }
            
            .chat-sidebar, .chat-main, .chat-main-header, .chat-input-container {
                background: #2d3748;
                border-color: #4a5568;
            }
            
            .conversation-item:hover {
                background: #374151;
            }
            
            .conversation-name {
                color: #f7fafc;
            }
            
            .chat-contact-details h4 {
                color: #f7fafc;
            }
            
            .messages-container {
                background: #1a202c;
            }
            
            .message.received .message-bubble {
                background: #374151;
                color: #f7fafc;
            }
            
            .chat-input {
                background: #374151;
                border-color: #4a5568;
                color: #f7fafc;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <!-- Debug info -->
            <div class="debug-info">
                <strong>Debug Info:</strong><br>
                Session ID: <?= $_SESSION['id'] ?><br>
                Session Role: <?= $_SESSION['role'] ?><br>
                Conversations found: <?= count($conversations) ?><br>
                <?php if (empty($conversations)) { ?>
                    Expected to see: <?= $_SESSION['role'] == 'admin' ? 'Employees' : 'Admins' ?>
                <?php } ?>
            </div>
            
            <div class="chat-container">
                <!-- Sidebar des conversations -->
                <div class="chat-sidebar" id="chatSidebar">
                    <div class="chat-header">
                        <h3><i class="fas fa-comments"></i> Messages</h3>
                        <p><?= $_SESSION['role'] == 'admin' ? 'Chat with employees' : 'Chat with admins' ?></p>
                    </div>
                    
                    <div class="conversations-list">
                        <?php if (empty($conversations)) { ?>
                            <div class="empty-conversations">
                                <i class="fas fa-users"></i>
                                <h4>No contacts available</h4>
                                <p>
                                    <?php if ($_SESSION['role'] == 'admin') { ?>
                                        No employees found in the system
                                    <?php } else { ?>
                                        No administrators found in the system
                                    <?php } ?>
                                </p>
                                <small style="margin-top: 10px; display: block;">
                                    Role: <?= $_SESSION['role'] ?> | ID: <?= $_SESSION['id'] ?>
                                </small>
                            </div>
                        <?php } else { ?>
                            <?php foreach ($conversations as $conversation) { ?>
                                <div class="conversation-item" onclick="selectConversation(<?= $conversation['id'] ?>, '<?= htmlspecialchars($conversation['full_name']) ?>', '<?= htmlspecialchars($conversation['username']) ?>')">
                                    <?php if ($conversation['unread_count'] > 0) { ?>
                                        <div class="unread-badge"><?= $conversation['unread_count'] ?></div>
                                    <?php } ?>
                                    <div class="conversation-info">
                                        <div class="conversation-name"><?= htmlspecialchars($conversation['full_name']) ?></div>
                                        <div class="conversation-time">
                                            <?= $conversation['last_message_time'] ? timeAgo($conversation['last_message_time']) : '' ?>
                                        </div>
                                    </div>
                                    <div class="conversation-preview">
                                        <?= $conversation['last_message'] ? htmlspecialchars($conversation['last_message']) : 'No messages yet - Click to start conversation' ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
                
                <!-- Zone de chat principale -->
                <div class="chat-main" id="chatMain">
                    <div class="empty-chat">
                        <i class="fas fa-comment-dots"></i>
                        <h3>Select a conversation</h3>
                        <p>Choose a contact from the sidebar to start chatting</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script>
        let currentContactId = null;
        let messageInterval = null;
        
        // Highlight active navigation
        document.querySelector("#navList li:nth-child(5)").classList.add("active");
        
        function selectConversation(contactId, fullName, username) {
            console.log('Selecting conversation with:', contactId, fullName, username);
            
            currentContactId = contactId;
            
            // Marquer comme actif
            document.querySelectorAll('.conversation-item').forEach(item => {
                item.classList.remove('active');
            });
            event.currentTarget.classList.add('active');
            
            // Créer l'interface de chat
            const chatMain = document.getElementById('chatMain');
            chatMain.innerHTML = `
                <div class="chat-main-header">
                    <div class="chat-contact-info">
                        <div class="chat-avatar">${fullName.charAt(0).toUpperCase()}</div>
                        <div class="chat-contact-details">
                            <h4>${fullName}</h4>
                            <p>@${username}</p>
                        </div>
                    </div>
                </div>
                <div class="messages-container" id="messagesContainer">
                    <div style="text-align: center; padding: 20px; color: #718096;">
                        <i class="fas fa-spinner fa-spin"></i> Chargement des messages...
                    </div>
                </div>
                <div class="chat-input-container">
                    <form class="chat-input-form" onsubmit="sendMessage(event)">
                        <textarea class="chat-input" id="messageInput" placeholder="Type your message..." rows="1" onkeydown="handleKeyDown(event)"></textarea>
                        <button type="submit" class="send-button" id="sendButton">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            `;
            
            // Charger les messages
            loadMessages();
            
            // Démarrer la mise à jour automatique
            if (messageInterval) {
                clearInterval(messageInterval);
            }
            messageInterval = setInterval(loadMessages, 3000);
            
            // Supprimer le badge non lu
            const badge = event.currentTarget.querySelector('.unread-badge');
            if (badge) {
                badge.remove();
            }
        }
        
        function loadMessages() {
            if (!currentContactId) return;
            
            console.log('Loading messages for contact:', currentContactId);
            
            fetch(`app/get-messages.php?contact_id=${currentContactId}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(text => {
                    console.log('Raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        if (data.success) {
                            displayMessages(data.messages || []);
                        } else {
                            console.error('API Error:', data.message);
                            showErrorMessage('Erreur: ' + (data.message || 'Erreur inconnue'));
                        }
                    } catch (e) {
                        console.error('JSON Parse Error:', e);
                        console.error('Response was:', text);
                        showErrorMessage('Réponse invalide du serveur');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showErrorMessage('Erreur réseau: ' + error.message);
                });
        }
        
        function showErrorMessage(message) {
            const container = document.getElementById('messagesContainer');
            if (container) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #ef4444;">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p style="margin-top: 16px;">${message}</p>
                        <button onclick="loadMessages()" style="margin-top: 16px; padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 8px; cursor: pointer;">
                            Réessayer
                        </button>
                    </div>
                `;
            }
        }
        
        function displayMessages(messages) {
            const container = document.getElementById('messagesContainer');
            if (!container) return;
            
            const wasScrolledToBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 1;
            
            container.innerHTML = '';
            
            if (messages.length === 0) {
                container.innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #718096;">
                        <i class="fas fa-comment"></i>
                        <p style="margin-top: 16px;">Aucun message encore. Commencez la conversation!</p>
                    </div>
                `;
                return;
            }
            
            messages.forEach(message => {
                const messageDiv = document.createElement('div');
                const isSent = message.sender_id == <?= $_SESSION['id'] ?>;
                const messageTime = new Date(message.created_at).toLocaleString('fr-FR', {
                    hour: '2-digit',
                    minute: '2-digit',
                    day: 'numeric',
                    month: 'short'
                });
                
                messageDiv.className = `message ${isSent ? 'sent' : 'received'}`;
                messageDiv.innerHTML = `
                    <div class="message-bubble">
                        ${escapeHtml(message.message)}
                        <div class="message-time">${messageTime}</div>
                    </div>
                `;
                
                container.appendChild(messageDiv);
            });
            
            if (wasScrolledToBottom) {
                container.scrollTop = container.scrollHeight;
            }
        }
        
        function sendMessage(event) {
            event.preventDefault();
            
            const messageInput = document.getElementById('messageInput');
            const sendButton = document.getElementById('sendButton');
            const message = messageInput.value.trim();
            
            if (!message || !currentContactId) return;
            
            sendButton.disabled = true;
            sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            const formData = new FormData();
            formData.append('receiver_id', currentContactId);
            formData.append('message', message);
            
            fetch('app/send-message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    loadMessages();
                    
                    // Mettre à jour la conversation dans la sidebar
                    updateConversationPreview(currentContactId, message);
                } else {
                    showCustomAlert('Erreur lors de l\'envoi: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCustomAlert('Erreur lors de l\'envoi du message');
            })
            .finally(() => {
                sendButton.disabled = false;
                sendButton.innerHTML = '<i class="fas fa-paper-plane"></i>';
                messageInput.focus();
            });
        }
        
        function showCustomAlert(message) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.right = '0';
            modal.style.bottom = '0';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.zIndex = '1000';
            modal.innerHTML = `
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 400px; width: 90%;">
                    <p style="margin: 0 0 16px 0; color: #374151;">${message}</p>
                    <div style="text-align: right;">
                        <button onclick="this.closest('div').remove()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">OK</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        function updateConversationPreview(contactId, message) {
            const conversationItems = document.querySelectorAll('.conversation-item');
            conversationItems.forEach(item => {
                if (item.onclick.toString().includes(contactId)) {
                    const preview = item.querySelector('.conversation-preview');
                    const time = item.querySelector('.conversation-time');
                    if (preview) preview.textContent = message;
                    if (time) time.textContent = 'maintenant';
                }
            });
        }
        
        function handleKeyDown(event) {
            if (event.key === 'Enter' && !event.shiftKey) {
                event.preventDefault();
                sendMessage(event);
            }
        }
        
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }
        
        // Auto-resize textarea
        document.addEventListener('input', function(event) {
            if (event.target.classList.contains('chat-input')) {
                event.target.style.height = 'auto';
                event.target.style.height = Math.min(event.target.scrollHeight, 100) + 'px';
            }
        });
        
        // Dark mode support
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
        
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
            if (event.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
</body>
</html>

<?php
} else {
    $em = "Please login first";
    header("Location: login.php?error=$em");
    exit();
}
?>