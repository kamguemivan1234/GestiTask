<header class="header">
	<h2 class="u-name">Task <b>Pro</b>
		<label for="checkbox">
			<i id="navbtn" class="fa fa-bars" aria-hidden="true"></i>
		</label>
	</h2>
	<span class="notification notification-bell" id="notificationBtn">
		<i class="fa fa-bell" aria-hidden="true"></i>
		<span id="notificationNum" class="notification-bell-badge"></span>
	</span>
</header>
<div class="notification-bar" id="notificationBar">
	<ul id="notifications">
	
	</ul>
</div>

<style>
	.notification {
		position: relative;
		cursor: pointer;
		transition: all 0.3s ease;
		padding: 8px;
		border-radius: 8px;
	}
	
	.notification:hover {
		transform: scale(1.05);
		background: rgba(102, 126, 234, 0.1);
		color: #667eea;
	}
	
	#notificationNum {
		position: absolute;
		top: -6px;
		right: -6px;
		background: linear-gradient(135deg, #ff6b6b, #ee5a52);
		color: white;
		border-radius: 50%;
		width: 18px;
		height: 18px;
		display: none;
		align-items: center;
		justify-content: center;
		font-size: 10px;
		font-weight: 700;
		border: 2px solid white;
		animation: pulse 2s infinite;
		z-index: 10;
	}
	
	#notificationNum.show {
		display: flex;
	}
	
	@keyframes pulse {
		0% {
			box-shadow: 0 0 0 0 rgba(255, 107, 107, 0.7);
		}
		70% {
			box-shadow: 0 0 0 6px rgba(255, 107, 107, 0);
		}
		100% {
			box-shadow: 0 0 0 0 rgba(255, 107, 107, 0);
		}
	}
</style>

<script type="text/javascript">
	var openNotification = false;

	const notification = ()=> {
		let notificationBar = document.querySelector("#notificationBar");
		if (openNotification) {
			notificationBar.classList.remove('open-notification');
			openNotification = false;
		}else {
			notificationBar.classList.add('open-notification');
			openNotification = true;
			// Recharger les notifications quand on ouvre
			$("#notifications").load("app/notification.php");
		}
	}
	let notificationBtn = document.querySelector("#notificationBtn");
	notificationBtn.addEventListener("click", notification);
</script>

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script type="text/javascript">
	$(document).ready(function(){
		// Fonction pour mettre à jour le compteur de notifications
		function updateNotificationCount() {
			$.get("app/notification-count.php", function(count) {
				const intCount = parseInt(count) || 0;
				const badge = $("#notificationNum");
				
				if (intCount > 0) {
					badge.text(intCount).addClass('show');
				} else {
					badge.removeClass('show').text('');
				}
			});
		}

		// Charger le compteur initial
		updateNotificationCount();
		
		// Charger les notifications initiales
		$("#notifications").load("app/notification.php");

		// Mettre à jour le compteur périodiquement
		setInterval(updateNotificationCount, 30000); // toutes les 30 secondes

		// Écouter les événements de mise à jour du compteur de notifications
		window.addEventListener('updateNotificationCount', function(event) {
			const count = event.detail.count;
			const badge = $("#notificationNum");
			
			if (count > 0) {
				badge.text(count).addClass('show');
			} else {
				badge.removeClass('show').text('');
			}
		});

		// Écouter les clics sur les notifications pour les marquer comme lues
		$(document).on('click', '.notification-link', function(e) {
			e.preventDefault();
			const notificationId = $(this).data('notification-id');
			
			if (notificationId) {
				// Marquer comme lue via AJAX
				$.post('app/notification-read-ajax.php', { 
					notification_id: notificationId 
				}, function(response) {
					if (response.success) {
						// Marquer visuellement comme lue
						const notificationItem = $('[data-notification-id="' + notificationId + '"]').closest('.notification-item');
						notificationItem.find('.notification-icon').removeClass('unread').addClass('read');
						notificationItem.find('.notification-type').removeClass('unread').addClass('read');
						notificationItem.find('.notification-message').removeClass('unread').addClass('read');
						notificationItem.find('.notification-badge').remove();
						
						// Mettre à jour le compteur
						const newCount = response.remaining_count;
						if (newCount > 0) {
							$("#notificationNum").text(newCount).addClass('show');
						} else {
							$("#notificationNum").removeClass('show').text('');
						}
					}
				}, 'json');
			}
		});
	});
</script>