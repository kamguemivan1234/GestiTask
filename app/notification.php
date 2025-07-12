<?php
// app/notification.php - VERSION MODIFIÉE
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
include "../DB_connection.php";
include "Model/Notification.php";

// Helper function for detailed time formatting
function formatDetailedTime($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00 00:00:00') {
        return 'Date inconnue';
    }
    
    $time = time() - strtotime($datetime);
    $formatted_date = date('d/m/Y à H:i:s', strtotime($datetime));
    
    if ($time < 60) {
        return $formatted_date . ' (il y a quelques secondes)';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $formatted_date . ' (il y a ' . $minutes . ' min)';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $formatted_date . ' (il y a ' . $hours . 'h)';
    } else {
        $days = floor($time / 86400);
        return $formatted_date . ' (il y a ' . $days . ' j)';
    }
}

function timeAgo($datetime) {
    if (empty($datetime) || $datetime == '0000-00-00') {
        return 'Unknown date';
    }

    $time = time() - strtotime($datetime);

    if ($time < 60) {
        return 'Just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . 'm ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . 'h ago';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . 'd ago';
    } else {
        return date('M j, Y', strtotime($datetime));
    }
}

$notifications = get_all_my_notifications($conn, $_SESSION['id']);
$unread_count = count_notification($conn, $_SESSION['id']);
?>

<style>
/* Modern notification dropdown styles - ALWAYS ON TOP */
.notification-dropdown {
    position: fixed !important;
    top: 70px !important;
    right: 20px !important;
    width: 400px;
    max-height: 500px;
    background: rgba(255, 255, 255, 0.98) !important;
    backdrop-filter: blur(20px) !important;
    border-radius: 16px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3) !important;
    border: 1px solid rgba(255, 255, 255, 0.2);
    z-index: 2147483647 !important;
    overflow: hidden;
    animation: slideDownFade 0.3s ease-out;
    margin-top: 0;
    transform: translateZ(0);
    will-change: transform;
    isolation: isolate;
}

.notification-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    z-index: 2147483646 !important;
    pointer-events: none;
}

@keyframes slideDownFade {
    from {
        opacity: 0;
        transform: translateY(-10px) translateZ(0);
    }
    to {
        opacity: 1;
        transform: translateY(0) translateZ(0);
    }
}

.notification-header {
    padding: 20px;
    border-bottom: 1px solid rgba(226, 232, 240, 0.5);
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    position: relative;
    z-index: 1;
}

.notification-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.notification-count {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
}

.notification-list {
    max-height: 400px;
    overflow-y: auto;
    padding: 0;
    margin: 0;
    list-style: none;
    position: relative;
    z-index: 1;
}

.notification-list::-webkit-scrollbar {
    width: 4px;
}

.notification-list::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.05);
}

.notification-list::-webkit-scrollbar-thumb {
    background: rgba(102, 126, 234, 0.3);
    border-radius: 2px;
}

.notification-item {
    border-bottom: 1px solid rgba(226, 232, 240, 0.3);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-item:hover {
    background: rgba(102, 126, 234, 0.05);
}

.notification-link {
    display: block;
    padding: 16px 20px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
    cursor: pointer;
}

.notification-content {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.notification-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    margin-top: 2px;
}

.notification-icon.unread {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.notification-icon.read {
    background: rgba(160, 174, 192, 0.15);
    color: #a0aec0;
}

.notification-body {
    flex: 1;
    min-width: 0;
}

.notification-type {
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}

.notification-type.unread {
    color: #667eea;
}

.notification-type.read {
    color: #a0aec0;
}

.notification-message {
    font-size: 14px;
    line-height: 1.4;
    color: #4a5568;
    margin-bottom: 6px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.notification-message.unread {
    font-weight: 500;
    color: #2d3748;
}

.notification-date {
    font-size: 11px;
    color: #a0aec0;
    font-weight: 500;
    margin-bottom: 8px;
}

.notification-times {
    font-size: 10px;
    color: #9ca3af;
    line-height: 1.3;
}

.notification-times .time-label {
    font-weight: 600;
    color: #6b7280;
}

.notification-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.notification-action-btn {
    padding: 4px 8px;
    border: none;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.delete-btn {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.delete-btn:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.read-btn {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.read-btn:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.notification-badge {
    position: absolute;
    top: 12px;
    right: 16px;
    width: 8px;
    height: 8px;
    background: #48bb78;
    border-radius: 50%;
    border: 2px solid white;
}

.empty-notifications {
    padding: 40px 20px;
    text-align: center;
    color: #718096;
    position: relative;
    z-index: 1;
}

.empty-icon {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, #e2e8f0, #cbd5e0);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 24px;
    color: #a0aec0;
}

.empty-title {
    font-size: 16px;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 8px;
}

.empty-message {
    font-size: 14px;
    color: #718096;
    margin: 0;
}

.notification-footer {
    padding: 16px 20px;
    border-top: 1px solid rgba(226, 232, 240, 0.5);
    background: rgba(247, 250, 252, 0.5);
    text-align: center;
    position: relative;
    z-index: 1;
}

.view-all-link {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    transition: color 0.3s ease;
}

.view-all-link:hover {
    color: #553c9a;
    text-decoration: none;
}

/* Modal de confirmation */
.confirm-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 2147483648;
}

.confirm-modal-content {
    background: white;
    padding: 25px;
    border-radius: 16px;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
    max-width: 400px;
    width: 90%;
    text-align: center;
}

.confirm-modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 10px;
}

.confirm-modal-message {
    font-size: 14px;
    color: #4a5568;
    margin-bottom: 20px;
    line-height: 1.5;
}

.confirm-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
}

.confirm-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
}

.confirm-btn.cancel {
    background: #e2e8f0;
    color: #4a5568;
}

.confirm-btn.cancel:hover {
    background: #cbd5e0;
}

.confirm-btn.delete {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: white;
}

.confirm-btn.delete:hover {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    transform: translateY(-1px);
}

/* Success notification */
.success-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(16, 185, 129, 0.3);
    z-index: 2147483649;
    animation: slideInRight 0.3s ease-out;
    max-width: 350px;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.success-notification .success-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.success-notification .success-message {
    font-size: 13px;
    opacity: 0.9;
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .notification-dropdown {
        background: rgba(26, 32, 44, 0.98) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5) !important;
    }

    .notification-header {
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }

    .notification-item {
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }

    .notification-item:hover {
        background: rgba(102, 126, 234, 0.1);
    }

    .notification-message {
        color: #cbd5e0;
    }

    .notification-message.unread {
        color: #f7fafc;
    }

    .notification-footer {
        background: rgba(26, 32, 44, 0.8);
        border-top-color: rgba(255, 255, 255, 0.1);
    }

    .empty-title {
        color: #e2e8f0;
    }

    .empty-message {
        color: #a0aec0;
    }

    .confirm-modal-content {
        background: #2d3748;
        color: #f7fafc;
    }

    .confirm-modal-title {
        color: #f7fafc;
    }

    .confirm-modal-message {
        color: #cbd5e0;
    }
}

/* Responsive adjustments */
@media (max-width: 480px) {
    .notification-dropdown {
        width: calc(100vw - 40px) !important;
        right: 20px !important;
        left: 20px !important;
    }
}

@media (max-width: 420px) {
    .notification-dropdown {
        width: calc(100vw - 20px) !important;
        right: 10px !important;
        left: 10px !important;
    }
}
</style>

<!-- Overlay to ensure dropdown stays on top -->
<div class="notification-overlay"></div>

<div class="notification-dropdown">
    <div class="notification-header">
        <h3 class="notification-title">
            <span>
                <i class="fas fa-bell"></i>
                Notifications
            </span>
            <?php if ($unread_count > 0) { ?>
                <span class="notification-count"><?= $unread_count ?> nouvelles</span>
            <?php } ?>
        </h3>
    </div>

    <?php if ($notifications == 0) { ?>
        <div class="empty-notifications">
            <div class="empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <div class="empty-title">Aucune notification</div>
            <p class="empty-message">Vous êtes à jour ! Les nouvelles notifications apparaîtront ici.</p>
        </div>
    <?php } else { ?>
        <ul class="notification-list">
            <?php foreach ($notifications as $notification) {
                $is_unread = $notification['is_read'] == 0;
                $notification_icon = '';
                
                // Determine icon based on notification type
                switch (strtolower($notification['type'])) {
                    case 'task':
                    case 'new task assigned':
                        $notification_icon = 'fas fa-tasks';
                        break;
                    case 'message':
                        $notification_icon = 'fas fa-envelope';
                        break;
                    case 'system':
                        $notification_icon = 'fas fa-cog';
                        break;
                    case 'reminder':
                        $notification_icon = 'fas fa-clock';
                        break;
                    default:
                        $notification_icon = 'fas fa-info-circle';
                        break;
                }
                
                // Calcul des timestamps détaillés
                $created_time = isset($notification['created_at']) ? 
                    formatDetailedTime($notification['created_at']) : 
                    formatDetailedTime($notification['date']);
                    
                $read_time = isset($notification['read_at']) && $notification['read_at'] ? 
                    formatDetailedTime($notification['read_at']) : null;
            ?>
                <li class="notification-item">
                    <div class="notification-link" data-notification-id="<?= $notification['id'] ?>">
                        <div class="notification-content">
                            <div class="notification-icon <?= $is_unread ? 'unread' : 'read' ?>">
                                <i class="<?= $notification_icon ?>"></i>
                            </div>
                            <div class="notification-body">
                                <div class="notification-type <?= $is_unread ? 'unread' : 'read' ?>">
                                    <?= htmlspecialchars($notification['type']) ?>
                                </div>
                                <div class="notification-message <?= $is_unread ? 'unread' : 'read' ?>">
                                    <?= htmlspecialchars($notification['message']) ?>
                                </div>
                                <div class="notification-date">
                                    <?= timeAgo($notification['date']) ?>
                                </div>
                                
                                <!-- Timestamps détaillés -->
                                <div class="notification-times">
                                    <div><span class="time-label">Reçue:</span> <?= $created_time ?></div>
                                    <?php if ($read_time) { ?>
                                        <div><span class="time-label">Lue:</span> <?= $read_time ?></div>
                                    <?php } ?>
                                </div>
                                
                                <!-- Actions -->
                                <div class="notification-actions">
                                    <?php if ($is_unread) { ?>
                                        <button class="notification-action-btn read-btn" onclick="markAsRead(<?= $notification['id'] ?>, event)">
                                            <i class="fas fa-check"></i> Marquer lu
                                        </button>
                                    <?php } ?>
                                    <button class="notification-action-btn delete-btn" onclick="confirmDelete(<?= $notification['id'] ?>, '<?= htmlspecialchars(addslashes($notification['message'])) ?>', event)">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php if ($is_unread) { ?>
                            <div class="notification-badge"></div>
                        <?php } ?>
                    </div>
                </li>
            <?php } ?>
        </ul>

        <div class="notification-footer">
            <a href="notifications.php" class="view-all-link">
                <i class="fas fa-list"></i>
                Voir toutes les notifications
            </a>
        </div>
    <?php } ?>
</div>

<?php
} else {
    echo "";
}
?>

<script>
// Support du mode sombre pour les notifications
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

// Fonction pour afficher une modal de confirmation personnalisée
function showConfirmModal(title, message, onConfirm) {
    const modal = document.createElement('div');
    modal.className = 'confirm-modal';
    modal.innerHTML = `
        <div class="confirm-modal-content">
            <h3 class="confirm-modal-title">${title}</h3>
            <p class="confirm-modal-message">${message}</p>
            <div class="confirm-modal-actions">
                <button class="confirm-btn cancel" onclick="this.closest('.confirm-modal').remove()">Annuler</button>
                <button class="confirm-btn delete" onclick="this.closest('.confirm-modal').remove(); (${onConfirm})()">Supprimer</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Fonction pour afficher une notification de succès
function showSuccessNotification(title, message) {
    const notification = document.createElement('div');
    notification.className = 'success-notification';
    notification.innerHTML = `
        <div class="success-title">${title}</div>
        <div class="success-message">${message}</div>
    `;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 4000);
}

// Fonction pour confirmer la suppression
function confirmDelete(notificationId, message, event) {
    event.stopPropagation();
    event.preventDefault();
    
    const shortMessage = message.length > 50 ? message.substring(0, 50) + '...' : message;
    
    showConfirmModal(
        'Confirmer la suppression',
        `Êtes-vous sûr de vouloir supprimer cette notification ?\n\n"${shortMessage}"`,
        `deleteNotification(${notificationId})`
    );
}

// Fonction pour supprimer une notification
function deleteNotification(notificationId) {
    console.log('Suppression de la notification:', notificationId);

    fetch('app/notification-delete-ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(data => {
        console.log('Réponse suppression:', data);
        
        if (data.success) {
            // Supprimer visuellement la notification
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`).closest('.notification-item');
            notificationItem.style.animation = 'fadeOut 0.3s ease-out';
            
            setTimeout(() => {
                notificationItem.remove();
                
                // Vérifier s'il reste des notifications
                const remainingNotifications = document.querySelectorAll('.notification-item');
                if (remainingNotifications.length === 0) {
                    // Afficher l'état vide
                    const notificationList = document.querySelector('.notification-list');
                    const notificationFooter = document.querySelector('.notification-footer');
                    
                    if (notificationList) notificationList.remove();
                    if (notificationFooter) notificationFooter.remove();
                    
                    const dropdown = document.querySelector('.notification-dropdown');
                    dropdown.innerHTML += `
                        <div class="empty-notifications">
                            <div class="empty-icon">
                                <i class="fas fa-bell-slash"></i>
                            </div>
                            <div class="empty-title">Aucune notification</div>
                            <p class="empty-message">Vous êtes à jour ! Les nouvelles notifications apparaîtront ici.</p>
                        </div>
                    `;
                }
            }, 300);

            // Mettre à jour les compteurs
            updateNotificationBadge(data.remaining_count);
            updateDropdownHeader(data.remaining_count);

            // Afficher notification de succès avec timestamp
            showSuccessNotification(
                'Notification supprimée',
                `Supprimée le ${data.deletion_time}`
            );

        } else {
            console.error('Erreur:', data.message);
            showSuccessNotification('Erreur', data.message);
        }
    })
    .catch(error => {
        console.error('Erreur réseau:', error);
        showSuccessNotification('Erreur', 'Impossible de supprimer la notification');
    });
}

// Fonction pour marquer comme lu
function markAsRead(notificationId, event) {
    event.stopPropagation();
    event.preventDefault();
    
    markNotificationAsRead(notificationId, event.target.closest('.notification-link'));
}

// Fonction pour marquer une notification comme lue via AJAX
function markNotificationAsRead(notificationId, linkElement) {
    console.log('Marquage comme lue:', notificationId);

    fetch('app/notification-read-ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'notification_id=' + notificationId
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(data => {
        console.log('Réponse:', data);
        
        if (data.success) {
            // Marquer visuellement la notification comme lue
            const notificationItem = linkElement.closest('.notification-item');
            const icon = notificationItem.querySelector('.notification-icon');
            const type = notificationItem.querySelector('.notification-type');
            const message = notificationItem.querySelector('.notification-message');
            const badge = notificationItem.querySelector('.notification-badge');
            const readBtn = notificationItem.querySelector('.read-btn');

            // Changer les classes pour l'état "lu"
            if (icon && icon.classList.contains('unread')) {
                icon.classList.remove('unread');
                icon.classList.add('read');
            }

            if (type && type.classList.contains('unread')) {
                type.classList.remove('unread');
                type.classList.add('read');
            }

            if (message && message.classList.contains('unread')) {
                message.classList.remove('unread');
                message.classList.add('read');
            }

            // Supprimer le badge et le bouton "Marquer lu"
            if (badge) badge.remove();
            if (readBtn) readBtn.remove();

            // Ajouter timestamp de lecture
            const times = notificationItem.querySelector('.notification-times');
            if (times) {
                const now = new Date();
                const readTime = now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR');
                times.innerHTML += `<div><span class="time-label">Lue:</span> ${readTime} (à l'instant)</div>`;
            }

            // Mettre à jour les compteurs
            updateNotificationBadge(data.remaining_count);
            updateDropdownHeader(data.remaining_count);

            // Notification de succès
            showSuccessNotification(
                'Notification marquée comme lue',
                `Lue le ${new Date().toLocaleDateString('fr-FR')} à ${new Date().toLocaleTimeString('fr-FR')}`
            );

        } else {
            console.error('Erreur lors de la mise à jour:', data.message);
        }
    })
    .catch(error => {
        console.error('Erreur réseau:', error);
    });
}

// Fonction pour mettre à jour le badge de notification dans l'en-tête principal
function updateNotificationBadge(count) {
    const headerBadge = document.querySelector("#notificationNum");
    
    if (count == 0) {
        if (headerBadge) {
            headerBadge.style.display = 'none';
            headerBadge.textContent = '';
            headerBadge.classList.remove('show');
        }
        window.dispatchEvent(new CustomEvent('updateNotificationCount', {
            detail: { count: 0 }
        }));
    } else {
        if (headerBadge) {
            headerBadge.style.display = 'flex';
            headerBadge.textContent = count;
            headerBadge.classList.add('show');
        }
        window.dispatchEvent(new CustomEvent('updateNotificationCount', {
            detail: { count: count }
        }));
    }

    if (typeof $ !== 'undefined') {
        const badge = $("#notificationNum");
        if (count > 0) {
            badge.text(count).addClass('show').show();
        } else {
            badge.removeClass('show').hide().text('');
        }
    }
}

// Fonction pour mettre à jour l'en-tête du dropdown
function updateDropdownHeader(count) {
    const headerCount = document.querySelector('.notification-count');
    
    if (count == 0) {
        if (headerCount) {
            headerCount.remove();
        }
    } else {
        if (headerCount) {
            headerCount.textContent = count + ' nouvelles';
        } else {
            const title = document.querySelector('.notification-title');
            if (title) {
                const newCount = document.createElement('span');
                newCount.className = 'notification-count';
                newCount.textContent = count + ' nouvelles';
                title.appendChild(newCount);
            }
        }
    }
}

// CSS pour l'animation de suppression
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(-20px); }
    }
`;
document.head.appendChild(style);

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.querySelector('.notification-dropdown');
    if (dropdown) {
        dropdown.style.zIndex = '2147483647';
        dropdown.style.position = 'fixed';

        let parent = dropdown.parentElement;
        while (parent && parent !== document.body) {
            if (window.getComputedStyle(parent).overflow === 'hidden') {
                parent.style.overflow = 'visible';
            }
            parent = parent.parentElement;
        }
    }

    // Fermer le dropdown en cliquant ailleurs
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.notification-dropdown');
        const overlay = document.querySelector('.notification-overlay');
        const notificationBtn = document.querySelector('#notificationBtn');

        if (dropdown && !dropdown.contains(event.target) &&
            notificationBtn && !notificationBtn.contains(event.target)) {
            if (dropdown.parentNode) {
                dropdown.parentNode.removeChild(dropdown);
            }
            if (overlay && overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }

            const notificationBar = document.querySelector("#notificationBar");
            if (notificationBar && notificationBar.classList.contains('open-notification')) {
                notificationBar.classList.remove('open-notification');
                window.openNotification = false;
            }
        }
    });
});
</script>