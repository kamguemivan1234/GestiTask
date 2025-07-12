<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Notification.php";

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

    $notifications = get_all_my_notifications($conn, $_SESSION['id']);
?>
<!DOCTYPE html>
<html>
<head>
	<title>Notifications</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
	<link rel="stylesheet" href="css/style.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		* {
			font-family: 'Inter', sans-serif;
		}

		.section-1 {
			background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
			min-height: 100vh;
			padding: 30px;
		}

		.page-header {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			padding: 30px;
			margin-bottom: 30px;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
			border: 1px solid rgba(255, 255, 255, 0.2);
			display: flex;
			justify-content: space-between;
			align-items: center;
		}

		.page-title {
			font-size: 32px;
			font-weight: 700;
			color: #2d3748;
			margin: 0;
			display: flex;
			align-items: center;
			gap: 15px;
		}

		.page-subtitle {
			font-size: 16px;
			color: #718096;
			margin-top: 8px;
			font-weight: 400;
		}

		.notifications-stats {
			display: flex;
			gap: 20px;
			align-items: center;
		}

		.stat-badge {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			padding: 8px 16px;
			border-radius: 12px;
			font-size: 14px;
			font-weight: 600;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.success-alert {
			background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
			color: #2f855a;
			padding: 20px 25px;
			margin-bottom: 25px;
			border: none;
			border-radius: 16px;
			border-left: 4px solid #38a169;
			font-weight: 500;
			animation: slideDown 0.5s ease-out;
			box-shadow: 0 4px 15px rgba(56, 161, 105, 0.2);
			display: flex;
			align-items: center;
			gap: 12px;
		}

		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-10px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.notifications-container {
			max-width: 900px;
			margin: 0 auto;
			animation: fadeInUp 0.8s ease-out;
		}

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(20px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.notifications-timeline {
			position: relative;
			padding-left: 40px;
		}

		.notifications-timeline::before {
			content: '';
			position: absolute;
			left: 20px;
			top: 0;
			bottom: 0;
			width: 2px;
			background: linear-gradient(180deg, #667eea, #764ba2);
			border-radius: 2px;
		}

		.notification-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			padding: 25px;
			margin-bottom: 20px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			position: relative;
			animation: slideInLeft 0.6s ease-out;
			animation-fill-mode: both;
		}

		.notification-card:nth-child(1) { animation-delay: 0.1s; }
		.notification-card:nth-child(2) { animation-delay: 0.2s; }
		.notification-card:nth-child(3) { animation-delay: 0.3s; }
		.notification-card:nth-child(4) { animation-delay: 0.4s; }
		.notification-card:nth-child(5) { animation-delay: 0.5s; }

		@keyframes slideInLeft {
			from {
				opacity: 0;
				transform: translateX(-30px);
			}
			to {
				opacity: 1;
				transform: translateX(0);
			}
		}

		.notification-card::before {
			content: '';
			position: absolute;
			left: -33px;
			top: 30px;
			width: 12px;
			height: 12px;
			border-radius: 50%;
			background: #667eea;
			border: 3px solid white;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
		}

		.notification-card:hover {
			transform: translateY(-5px) translateX(5px);
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
		}

		.notification-header {
			display: flex;
			align-items: flex-start;
			gap: 15px;
			margin-bottom: 15px;
		}

		.notification-icon {
			width: 50px;
			height: 50px;
			border-radius: 16px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 20px;
			flex-shrink: 0;
		}

		.notification-icon.info {
			background: linear-gradient(135deg, #3b82f6, #1d4ed8);
			color: white;
		}

		.notification-icon.success {
			background: linear-gradient(135deg, #10b981, #059669);
			color: white;
		}

		.notification-icon.warning {
			background: linear-gradient(135deg, #f59e0b, #d97706);
			color: white;
		}

		.notification-icon.error {
			background: linear-gradient(135deg, #ef4444, #dc2626);
			color: white;
		}

		.notification-icon.task {
			background: linear-gradient(135deg, #8b5cf6, #7c3aed);
			color: white;
		}

		.notification-content {
			flex: 1;
		}

		.notification-meta {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 10px;
		}

		.notification-number {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			width: 28px;
			height: 28px;
			border-radius: 8px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 12px;
			font-weight: 600;
		}

		.notification-type {
			padding: 6px 12px;
			border-radius: 8px;
			font-size: 12px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.notification-type.unread {
			color: #667eea;
		}

		.notification-type.read {
			color: #a0aec0;
		}

		.type-info {
			background: rgba(59, 130, 246, 0.1);
			color: #1d4ed8;
		}

		.type-success {
			background: rgba(16, 185, 129, 0.1);
			color: #059669;
		}

		.type-warning {
			background: rgba(245, 158, 11, 0.1);
			color: #d97706;
		}

		.type-error {
			background: rgba(239, 68, 68, 0.1);
			color: #dc2626;
		}

		.type-task {
			background: rgba(139, 92, 246, 0.1);
			color: #7c3aed;
		}

		.notification-message {
			font-size: 16px;
			color: #2d3748;
			line-height: 1.6;
			margin-bottom: 12px;
			font-weight: 500;
		}

		.notification-message.unread {
			font-weight: 600;
			color: #1a202c;
		}

		.notification-message.read {
			color: #4a5568;
		}

		.notification-date {
			display: flex;
			align-items: center;
			gap: 8px;
			color: #718096;
			font-size: 14px;
			font-weight: 500;
			margin-bottom: 8px;
		}

		.notification-times {
			font-size: 11px;
			color: #9ca3af;
			line-height: 1.4;
			margin-bottom: 12px;
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
			padding: 6px 12px;
			border: none;
			border-radius: 8px;
			font-size: 11px;
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

		.empty-state {
			text-align: center;
			padding: 80px 40px;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 24px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
		}

		.empty-state-icon {
			width: 100px;
			height: 100px;
			background: linear-gradient(135deg, #e2e8f0, #cbd5e0);
			border-radius: 24px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 25px;
			font-size: 40px;
			color: #a0aec0;
		}

		.empty-state h3 {
			color: #4a5568;
			font-size: 28px;
			font-weight: 600;
			margin-bottom: 12px;
		}

		.empty-state p {
			color: #718096;
			font-size: 16px;
			margin: 0;
			line-height: 1.6;
		}

		.filter-tabs {
			display: flex;
			gap: 10px;
			margin-bottom: 30px;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 16px;
			padding: 8px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
		}

		.filter-tab {
			padding: 12px 20px;
			border-radius: 12px;
			border: none;
			background: transparent;
			color: #718096;
			font-weight: 500;
			cursor: pointer;
			transition: all 0.3s ease;
			font-size: 14px;
		}

		.filter-tab.active,
		.filter-tab:hover {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			transform: translateY(-2px);
			box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
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
			z-index: 10000;
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
			z-index: 10001;
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

		/* Responsive */
		@media (max-width: 768px) {
			.section-1 {
				padding: 20px;
			}

			.page-header {
				padding: 25px;
				flex-direction: column;
				gap: 20px;
				text-align: center;
			}

			.page-title {
				font-size: 24px;
			}

			.notifications-timeline {
				padding-left: 20px;
			}

			.notifications-timeline::before {
				left: 10px;
			}

			.notification-card::before {
				left: -13px;
			}

			.notification-card {
				padding: 20px;
			}

			.notification-header {
				flex-direction: column;
				text-align: center;
				gap: 10px;
			}
		}

		/* Dark mode support */
		@media (prefers-color-scheme: dark) {
			.section-1 {
				background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
			}

			.page-header,
			.notification-card,
			.empty-state,
			.filter-tabs {
				background: rgba(45, 55, 72, 0.95);
				border: 1px solid rgba(255, 255, 255, 0.1);
			}

			.page-title,
			.notification-message {
				color: #f7fafc;
			}

			.page-subtitle,
			.notification-date {
				color: #cbd5e0;
			}

			.empty-state h3 {
				color: #f7fafc;
			}

			.empty-state p {
				color: #cbd5e0;
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
	</style>
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php" ?>
	<div class="body">
		<?php include "inc/nav.php" ?>
		<section class="section-1">
			<div class="page-header">
				<div>
					<h1 class="page-title">
						<i class="fas fa-bell"></i>
						Notifications
					</h1>
					<p class="page-subtitle">Restez informé de vos dernières activités</p>
				</div>
				<div class="notifications-stats">
					<div class="stat-badge">
						<i class="fas fa-inbox"></i>
						<?= $notifications != 0 ? count($notifications) : 0 ?> Total
					</div>
				</div>
			</div>

			<?php if (isset($_GET['success'])) {?>
				<div class="success-alert">
					<i class="fas fa-check-circle"></i>
					<?php echo stripcslashes($_GET['success']); ?>
				</div>
			<?php } ?>

			<div class="notifications-container">
				<?php if ($notifications != 0) { ?>
					<!-- Filter Tabs -->
					<div class="filter-tabs">
						<button class="filter-tab active" onclick="filterNotifications('all')">
							<i class="fas fa-list"></i> Toutes
						</button>
						<button class="filter-tab" onclick="filterNotifications('info')">
							<i class="fas fa-info-circle"></i> Info
						</button>
						<button class="filter-tab" onclick="filterNotifications('task')">
							<i class="fas fa-tasks"></i> Tâches
						</button>
						<button class="filter-tab" onclick="filterNotifications('success')">
							<i class="fas fa-check-circle"></i> Succès
						</button>
					</div>

					<div class="notifications-timeline">
						<?php $i = 0; foreach ($notifications as $notification) { 
							// Determine notification type and icon
							$type = strtolower($notification['type']);
							$iconClass = 'fas fa-bell';
							$typeClass = 'info';
							
							switch($type) {
								case 'success':
									$iconClass = 'fas fa-check-circle';
									$typeClass = 'success';
									break;
								case 'error':
									$iconClass = 'fas fa-exclamation-triangle';
									$typeClass = 'error';
									break;
								case 'warning':
									$iconClass = 'fas fa-exclamation-circle';
									$typeClass = 'warning';
									break;
								case 'task':
								case 'new task assigned':
									$iconClass = 'fas fa-tasks';
									$typeClass = 'task';
									break;
								default:
									$iconClass = 'fas fa-info-circle';
									$typeClass = 'info';
							}

							// Calcul des timestamps détaillés
							$created_time = isset($notification['created_at']) ? 
								formatDetailedTime($notification['created_at']) : 
								formatDetailedTime($notification['date'] . ' 00:00:00');
								
							$read_time = isset($notification['read_at']) && $notification['read_at'] ? 
								formatDetailedTime($notification['read_at']) : null;
						?>
						<div class="notification-card" data-type="<?= $type ?>" data-notification-id="<?= $notification['id'] ?>">
							<div class="notification-header">
								<div class="notification-icon <?= $typeClass ?>">
									<i class="<?= $iconClass ?>"></i>
								</div>
								<div class="notification-content">
									<div class="notification-meta">
										<div class="notification-number"><?= ++$i ?></div>
										<div class="notification-type type-<?= $typeClass ?> <?= $notification['is_read'] == 0 ? 'unread' : 'read' ?>">
											<?= htmlspecialchars($notification['type']) ?>
										</div>
									</div>
									<div class="notification-message <?= $notification['is_read'] == 0 ? 'unread' : 'read' ?>">
										<?= htmlspecialchars($notification['message']) ?>
									</div>
									<div class="notification-date">
										<i class="fas fa-clock"></i>
										<?= date('M d, Y à H:i:s', strtotime($notification['date'])) ?>
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
										<?php if ($notification['is_read'] == 0) { ?>
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
						</div>
						<?php } ?>
					</div>
				<?php } else { ?>
					<div class="empty-state">
						<div class="empty-state-icon">
							<i class="fas fa-bell-slash"></i>
						</div>
						<h3>Aucune notification</h3>
						<p>Vous êtes à jour ! Aucune nouvelle notification pour le moment.<br>Nous vous avertirons lorsque quelque chose d'important se produit.</p>
					</div>
				<?php } ?>
			</div>
		</section>
	</div>

	<script>
		// Highlight active navigation
		document.querySelector("#navList li:nth-child(4)").classList.add("active");

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
					const notificationCard = document.querySelector(`[data-notification-id="${notificationId}"]`);
					notificationCard.style.animation = 'fadeOut 0.3s ease-out';
					
					setTimeout(() => {
						notificationCard.remove();
						
						// Vérifier s'il reste des notifications
						const remainingNotifications = document.querySelectorAll('.notification-card');
						if (remainingNotifications.length === 0) {
							// Afficher l'état vide
							const timeline = document.querySelector('.notifications-timeline');
							const filterTabs = document.querySelector('.filter-tabs');
							
							if (timeline) timeline.remove();
							if (filterTabs) filterTabs.remove();
							
							const container = document.querySelector('.notifications-container');
							container.innerHTML += `
								<div class="empty-state">
									<div class="empty-state-icon">
										<i class="fas fa-bell-slash"></i>
									</div>
									<h3>Aucune notification</h3>
									<p>Vous êtes à jour ! Aucune nouvelle notification pour le moment.<br>Nous vous avertirons lorsque quelque chose d'important se produit.</p>
								</div>
							`;
						}
						
						// Mettre à jour le compteur dans les stats
						updateStatsCounter(data.remaining_count);
					}, 300);

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
			
			markNotificationAsRead(notificationId, event.target.closest('.notification-card'));
		}

		// Fonction pour marquer une notification comme lue
		function markNotificationAsRead(notificationId, cardElement) {
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
					const type = cardElement.querySelector('.notification-type');
					const message = cardElement.querySelector('.notification-message');
					const readBtn = cardElement.querySelector('.read-btn');

					// Changer les classes pour l'état "lu"
					if (type && type.classList.contains('unread')) {
						type.classList.remove('unread');
						type.classList.add('read');
					}

					if (message && message.classList.contains('unread')) {
						message.classList.remove('unread');
						message.classList.add('read');
					}

					// Supprimer le bouton "Marquer lu"
					if (readBtn) readBtn.remove();

					// Ajouter timestamp de lecture
					const times = cardElement.querySelector('.notification-times');
					if (times) {
						const now = new Date();
						const readTime = now.toLocaleDateString('fr-FR') + ' à ' + now.toLocaleTimeString('fr-FR');
						times.innerHTML += `<div><span class="time-label">Lue:</span> ${readTime} (à l'instant)</div>`;
					}

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

		// Mettre à jour le compteur dans les stats
		function updateStatsCounter(count) {
			const statBadge = document.querySelector('.stat-badge');
			if (statBadge) {
				statBadge.innerHTML = `<i class="fas fa-inbox"></i> ${count} Total`;
			}
		}

		// Filter notifications function
		function filterNotifications(type) {
			const cards = document.querySelectorAll('.notification-card');
			const tabs = document.querySelectorAll('.filter-tab');
			
			// Update active tab
			tabs.forEach(tab => tab.classList.remove('active'));
			event.target.closest('.filter-tab').classList.add('active');
			
			// Filter cards
			cards.forEach(card => {
				if (type === 'all' || card.dataset.type === type) {
					card.style.display = 'block';
					card.style.animation = 'slideInLeft 0.6s ease-out';
				} else {
					card.style.display = 'none';
				}
			});
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

		// Add interaction effects
		document.addEventListener('DOMContentLoaded', function() {
			const cards = document.querySelectorAll('.notification-card');
			
			cards.forEach(card => {
				card.addEventListener('click', function(e) {
					// Éviter l'animation si on clique sur un bouton
					if (e.target.closest('button')) return;
					
					this.style.transform = 'scale(0.98)';
					setTimeout(() => {
						this.style.transform = '';
					}, 150);
				});
			});

			// Scroll reveal animation
			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						entry.target.style.opacity = '1';
						entry.target.style.transform = 'translateX(0)';
					}
				});
			});

			cards.forEach(card => {
				observer.observe(card);
			});
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
<?php } else { 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
?>