<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
    include "DB_connection.php";
    include "app/Model/User.php";
    $user = get_user_by_id($conn, $_SESSION['id']);
?>
<!DOCTYPE html>
<html>
<head>
	<title>My Profile</title>
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
			text-align: center;
		}

		.page-title {
			font-size: 32px;
			font-weight: 700;
			color: #2d3748;
			margin: 0 0 10px 0;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 15px;
		}

		.page-subtitle {
			font-size: 16px;
			color: #718096;
			margin: 0;
			font-weight: 400;
		}

		.profile-container {
			max-width: 800px;
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

		.profile-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 24px;
			padding: 40px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
			border: 1px solid rgba(255, 255, 255, 0.2);
			position: relative;
			overflow: hidden;
			margin-bottom: 30px;
		}

		.profile-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
			border-radius: 24px 24px 0 0;
		}

		.profile-header {
			display: flex;
			align-items: center;
			gap: 30px;
			margin-bottom: 40px;
			padding-bottom: 30px;
			border-bottom: 1px solid #e2e8f0;
		}

		.avatar-container {
			position: relative;
		}

		.avatar {
			width: 120px;
			height: 120px;
			border-radius: 24px;
			background: linear-gradient(135deg, #667eea, #764ba2);
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 48px;
			color: white;
			font-weight: 600;
			box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
			transition: all 0.3s ease;
		}

		.avatar:hover {
			transform: scale(1.05);
			box-shadow: 0 20px 45px rgba(102, 126, 234, 0.4);
		}

		.status-badge {
			position: absolute;
			bottom: 10px;
			right: 10px;
			width: 20px;
			height: 20px;
			background: linear-gradient(135deg, #48bb78, #38a169);
			border: 3px solid white;
			border-radius: 50%;
			box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
		}

		.profile-info {
			flex: 1;
		}

		.profile-name {
			font-size: 28px;
			font-weight: 700;
			color: #2d3748;
			margin: 0 0 8px 0;
			line-height: 1.2;
		}

		.profile-role {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			padding: 8px 16px;
			border-radius: 12px;
			font-size: 14px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			display: inline-block;
			margin-bottom: 15px;
		}

		.profile-meta {
			color: #718096;
			font-size: 16px;
			display: flex;
			align-items: center;
			gap: 8px;
		}

		.profile-details {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
			gap: 25px;
		}

		.detail-item {
			background: rgba(255, 255, 255, 0.6);
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 25px;
			transition: all 0.3s ease;
		}

		.detail-item:hover {
			transform: translateY(-3px);
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
			border-color: #cbd5e0;
		}

		.detail-icon {
			width: 50px;
			height: 50px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 20px;
			margin-bottom: 15px;
		}

		.detail-icon.name {
			background: rgba(102, 126, 234, 0.1);
			color: #667eea;
		}

		.detail-icon.username {
			background: rgba(156, 163, 175, 0.1);
			color: #6b7280;
		}

		.detail-icon.date {
			background: rgba(34, 197, 94, 0.1);
			color: #22c55e;
		}

		.detail-label {
			font-size: 14px;
			font-weight: 600;
			color: #4a5568;
			margin-bottom: 8px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.detail-value {
			font-size: 18px;
			font-weight: 600;
			color: #2d3748;
			line-height: 1.4;
		}

		.actions-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			padding: 30px;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
			border: 1px solid rgba(255, 255, 255, 0.2);
			text-align: center;
		}

		.action-btn {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			padding: 16px 32px;
			border-radius: 16px;
			text-decoration: none;
			font-size: 16px;
			font-weight: 600;
			display: inline-flex;
			align-items: center;
			gap: 10px;
			transition: all 0.3s ease;
			border: none;
			cursor: pointer;
			box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
			letter-spacing: 0.5px;
		}

		.action-btn:hover {
			transform: translateY(-3px);
			box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
			color: white;
			text-decoration: none;
		}

		.stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 20px;
			margin-bottom: 30px;
		}

		.stat-card {
			background: rgba(255, 255, 255, 0.6);
			border: 1px solid #e2e8f0;
			border-radius: 16px;
			padding: 25px;
			text-align: center;
			transition: all 0.3s ease;
		}

		.stat-card:hover {
			transform: translateY(-3px);
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
		}

		.stat-icon {
			width: 60px;
			height: 60px;
			border-radius: 16px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 15px;
			font-size: 24px;
		}

		.stat-icon.tasks {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
		}

		.stat-icon.time {
			background: linear-gradient(135deg, #f093fb, #f5576c);
			color: white;
		}

		.stat-number {
			font-size: 24px;
			font-weight: 700;
			color: #2d3748;
			margin-bottom: 5px;
		}

		.stat-label {
			font-size: 14px;
			color: #718096;
			font-weight: 500;
		}

		/* Animations pour les éléments */
		.detail-item {
			animation: slideInUp 0.6s ease-out;
			animation-fill-mode: both;
		}

		.detail-item:nth-child(1) { animation-delay: 0.1s; }
		.detail-item:nth-child(2) { animation-delay: 0.2s; }
		.detail-item:nth-child(3) { animation-delay: 0.3s; }

		@keyframes slideInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		/* Responsive */
		@media (max-width: 768px) {
			.section-1 {
				padding: 20px;
			}

			.profile-card,
			.actions-card {
				padding: 25px;
			}

			.profile-header {
				flex-direction: column;
				text-align: center;
				gap: 20px;
			}

			.page-title {
				font-size: 24px;
			}

			.profile-name {
				font-size: 24px;
			}

			.profile-details {
				grid-template-columns: 1fr;
				gap: 20px;
			}

			.avatar {
				width: 100px;
				height: 100px;
				font-size: 40px;
			}
		}

		/* Dark mode support */
		@media (prefers-color-scheme: dark) {
			.section-1 {
				background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
			}

			.page-header,
			.profile-card,
			.actions-card {
				background: rgba(45, 55, 72, 0.95);
				border: 1px solid rgba(255, 255, 255, 0.1);
			}

			.detail-item,
			.stat-card {
				background: rgba(74, 85, 104, 0.6);
				border-color: #4a5568;
			}

			.page-title,
			.profile-name,
			.detail-value,
			.stat-number {
				color: #f7fafc;
			}

			.page-subtitle,
			.profile-meta,
			.detail-label,
			.stat-label {
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
				<h1 class="page-title">
					<i class="fas fa-user-circle"></i>
					My Profile
				</h1>
				<p class="page-subtitle">Manage your account information and settings</p>
			</div>

			<div class="profile-container">
				<!-- Stats Cards -->
				<div class="stats-grid">
					<div class="stat-card">
						<div class="stat-icon tasks">
							<i class="fas fa-tasks"></i>
						</div>
						<div class="stat-number">12</div>
						<div class="stat-label">Active Tasks</div>
					</div>
					<div class="stat-card">
						<div class="stat-icon time">
							<i class="fas fa-clock"></i>
						</div>
						<div class="stat-number"><?= date('d', strtotime($user['created_at'])) ?></div>
						<div class="stat-label">Days Since Joined</div>
					</div>
				</div>

				<!-- Profile Card -->
				<div class="profile-card">
					<div class="profile-header">
						<div class="avatar-container">
							<div class="avatar">
								<?= strtoupper(substr($user['full_name'], 0, 1)) ?>
							</div>
							<div class="status-badge" title="Online"></div>
						</div>
						<div class="profile-info">
							<h2 class="profile-name"><?= htmlspecialchars($user['full_name']) ?></h2>
							<div class="profile-role">
								<i class="fas fa-user-tie"></i>
								Employee
							</div>
							<div class="profile-meta">
								<i class="fas fa-calendar-check"></i>
								Member since <?= date('F Y', strtotime($user['created_at'])) ?>
							</div>
						</div>
					</div>

					<div class="profile-details">
						<div class="detail-item">
							<div class="detail-icon name">
								<i class="fas fa-user"></i>
							</div>
							<div class="detail-label">Full Name</div>
							<div class="detail-value"><?= htmlspecialchars($user['full_name']) ?></div>
						</div>

						<div class="detail-item">
							<div class="detail-icon username">
								<i class="fas fa-at"></i>
							</div>
							<div class="detail-label">Username</div>
							<div class="detail-value">@<?= htmlspecialchars($user['username']) ?></div>
						</div>

						<div class="detail-item">
							<div class="detail-icon date">
								<i class="fas fa-calendar-plus"></i>
							</div>
							<div class="detail-label">Joined On</div>
							<div class="detail-value"><?= date('F d, Y', strtotime($user['created_at'])) ?></div>
						</div>
					</div>
				</div>

				<!-- Actions Card -->
				<div class="actions-card">
					<h3 style="margin-bottom: 20px; color: #2d3748; font-weight: 600;">Quick Actions</h3>
					<a href="edit_profile.php" class="action-btn">
						<i class="fas fa-edit"></i>
						Edit Profile
					</a>
				</div>
			</div>
		</section>
	</div>

	<script>
		// Highlight active navigation
		document.querySelector("#navList li:nth-child(3)").classList.add("active");

		// Add interactive effects
		document.addEventListener('DOMContentLoaded', function() {
			// Animate stats on load
			const statNumbers = document.querySelectorAll('.stat-number');
			statNumbers.forEach(stat => {
				const finalValue = parseInt(stat.textContent);
				let currentValue = 0;
				const increment = finalValue / 30;
				
				const timer = setInterval(() => {
					currentValue += increment;
					if (currentValue >= finalValue) {
						stat.textContent = finalValue;
						clearInterval(timer);
					} else {
						stat.textContent = Math.floor(currentValue);
					}
				}, 50);
			});

			// Add loading state to action button
			const actionBtn = document.querySelector('.action-btn');
			actionBtn.addEventListener('click', function() {
				this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
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
<?php 
} else { 
   $em = "Please login first";
   header("Location: login.php?error=$em");
   exit();
}
?>