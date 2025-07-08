<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

	include "DB_connection.php";
	include "app/Model/Task.php";
	include "app/Model/User.php";

	if ($_SESSION['role'] == "admin") {
		$todaydue_task   = count_tasks_due_today($conn);
		$overdue_task    = count_tasks_overdue($conn);
		$nodeadline_task = count_tasks_NoDeadline($conn);
		$num_task        = count_tasks($conn);
		$num_users       = count_users($conn);
		$pending         = count_pending_tasks($conn);
		$in_progress     = count_in_progress_tasks($conn);
		$completed       = count_completed_tasks($conn);
	} else {
		$num_my_task     = count_my_tasks($conn, $_SESSION['id']);
		$overdue_task    = count_my_tasks_overdue($conn, $_SESSION['id']);
		$nodeadline_task = count_my_tasks_NoDeadline($conn, $_SESSION['id']);
		$pending         = count_my_pending_tasks($conn, $_SESSION['id']);
		$in_progress     = count_my_in_progress_tasks($conn, $_SESSION['id']);
		$completed       = count_my_completed_tasks($conn, $_SESSION['id']);
	}

	// Calculer les pourcentages pour les graphiques
	if ($_SESSION['role'] == "admin") {
		$total_tasks = $pending + $in_progress + $completed;
		$pending_percent = $total_tasks > 0 ? round(($pending / $total_tasks) * 100) : 0;
		$progress_percent = $total_tasks > 0 ? round(($in_progress / $total_tasks) * 100) : 0;
		$completed_percent = $total_tasks > 0 ? round(($completed / $total_tasks) * 100) : 0;
	} else {
		$total_my_tasks = $pending + $in_progress + $completed;
		$pending_percent = $total_my_tasks > 0 ? round(($pending / $total_my_tasks) * 100) : 0;
		$progress_percent = $total_my_tasks > 0 ? round(($in_progress / $total_my_tasks) * 100) : 0;
		$completed_percent = $total_my_tasks > 0 ? round(($completed / $total_my_tasks) * 100) : 0;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Dashboard</title>
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
			position: relative;
		}

		.section-1::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/></svg>') no-repeat center center;
			background-size: cover;
			pointer-events: none;
		}

		.dashboard-container {
			position: relative;
			z-index: 1;
			max-width: 1400px;
			margin: 0 auto;
		}

		.welcome-header {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 24px;
			padding: 40px;
			margin-bottom: 30px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
			border: 1px solid rgba(255, 255, 255, 0.2);
			position: relative;
			overflow: hidden;
			animation: slideDown 0.8s ease-out;
		}

		@keyframes slideDown {
			from {
				opacity: 0;
				transform: translateY(-30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.welcome-header::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
			border-radius: 24px 24px 0 0;
		}

		.welcome-content {
			display: flex;
			justify-content: space-between;
			align-items: center;
			gap: 30px;
		}

		.welcome-text h1 {
			font-size: 36px;
			font-weight: 700;
			color: #2d3748;
			margin: 0 0 10px 0;
			line-height: 1.2;
		}

		.welcome-text p {
			font-size: 18px;
			color: #718096;
			margin: 0 0 20px 0;
			line-height: 1.5;
		}

		.role-badge {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			padding: 8px 20px;
			border-radius: 12px;
			font-size: 14px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			display: inline-block;
		}

		.welcome-time {
			text-align: right;
			color: #718096;
		}

		.current-time {
			font-size: 24px;
			font-weight: 600;
			color: #2d3748;
			margin-bottom: 5px;
		}

		.current-date {
			font-size: 16px;
			color: #718096;
		}

		.dashboard-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
			gap: 25px;
			margin-bottom: 30px;
		}

		.stat-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			padding: 30px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			position: relative;
			overflow: hidden;
			animation: fadeInUp 0.8s ease-out;
			animation-fill-mode: both;
		}

		.stat-card:nth-child(1) { animation-delay: 0.1s; }
		.stat-card:nth-child(2) { animation-delay: 0.2s; }
		.stat-card:nth-child(3) { animation-delay: 0.3s; }
		.stat-card:nth-child(4) { animation-delay: 0.4s; }
		.stat-card:nth-child(5) { animation-delay: 0.5s; }
		.stat-card:nth-child(6) { animation-delay: 0.6s; }
		.stat-card:nth-child(7) { animation-delay: 0.7s; }
		.stat-card:nth-child(8) { animation-delay: 0.8s; }

		@keyframes fadeInUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.stat-card:hover {
			transform: translateY(-8px);
			box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
		}

		.stat-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			opacity: 0;
			transition: opacity 0.3s ease;
		}

		.stat-card:hover::before {
			opacity: 1;
		}

		.stat-card.users::before {
			background: linear-gradient(90deg, #3b82f6, #1d4ed8);
		}

		.stat-card.tasks::before {
			background: linear-gradient(90deg, #8b5cf6, #7c3aed);
		}

		.stat-card.due-today::before {
			background: linear-gradient(90deg, #f59e0b, #d97706);
		}

		.stat-card.overdue::before {
			background: linear-gradient(90deg, #ef4444, #dc2626);
		}

		.stat-card.no-deadline::before {
			background: linear-gradient(90deg, #6b7280, #4b5563);
		}

		.stat-card.pending::before {
			background: linear-gradient(90deg, #f97316, #ea580c);
		}

		.stat-card.progress::before {
			background: linear-gradient(90deg, #06b6d4, #0891b2);
		}

		.stat-card.completed::before {
			background: linear-gradient(90deg, #10b981, #059669);
		}

		.stat-header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 20px;
		}

		.stat-icon {
			width: 60px;
			height: 60px;
			border-radius: 16px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 24px;
			color: white;
			position: relative;
		}

		.stat-icon.users {
			background: linear-gradient(135deg, #3b82f6, #1d4ed8);
		}

		.stat-icon.tasks {
			background: linear-gradient(135deg, #8b5cf6, #7c3aed);
		}

		.stat-icon.due-today {
			background: linear-gradient(135deg, #f59e0b, #d97706);
		}

		.stat-icon.overdue {
			background: linear-gradient(135deg, #ef4444, #dc2626);
		}

		.stat-icon.no-deadline {
			background: linear-gradient(135deg, #6b7280, #4b5563);
		}

		.stat-icon.pending {
			background: linear-gradient(135deg, #f97316, #ea580c);
		}

		.stat-icon.progress {
			background: linear-gradient(135deg, #06b6d4, #0891b2);
		}

		.stat-icon.completed {
			background: linear-gradient(135deg, #10b981, #059669);
		}

		.stat-trend {
			background: rgba(16, 185, 129, 0.1);
			color: #059669;
			padding: 4px 8px;
			border-radius: 8px;
			font-size: 12px;
			font-weight: 600;
		}

		.stat-number {
			font-size: 36px;
			font-weight: 700;
			color: #2d3748;
			margin-bottom: 8px;
			line-height: 1;
		}

		.stat-label {
			font-size: 16px;
			color: #718096;
			font-weight: 500;
			margin-bottom: 15px;
		}

		.stat-progress {
			width: 100%;
			height: 6px;
			background: #e2e8f0;
			border-radius: 6px;
			overflow: hidden;
			margin-bottom: 10px;
		}

		.stat-progress-bar {
			height: 100%;
			border-radius: 6px;
			transition: width 1s ease-out;
			position: relative;
		}

		.stat-progress-bar.users {
			background: linear-gradient(90deg, #3b82f6, #1d4ed8);
		}

		.stat-progress-bar.tasks {
			background: linear-gradient(90deg, #8b5cf6, #7c3aed);
		}

		.stat-progress-bar.pending {
			background: linear-gradient(90deg, #f97316, #ea580c);
		}

		.stat-progress-bar.progress {
			background: linear-gradient(90deg, #06b6d4, #0891b2);
		}

		.stat-progress-bar.completed {
			background: linear-gradient(90deg, #10b981, #059669);
		}

		.stat-description {
			font-size: 13px;
			color: #a0aec0;
			font-style: italic;
		}

		.dashboard-sections {
			display: grid;
			grid-template-columns: 2fr 1fr;
			gap: 30px;
			margin-top: 30px;
		}

		.chart-section {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			padding: 30px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
		}

		.chart-title {
			font-size: 20px;
			font-weight: 600;
			color: #2d3748;
			margin-bottom: 25px;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.progress-chart {
			position: relative;
			height: 200px;
			display: flex;
			align-items: center;
			justify-content: center;
		}

		.chart-circle {
			width: 150px;
			height: 150px;
			border-radius: 50%;
			background: conic-gradient(
				#10b981 0deg <?= $completed_percent * 3.6 ?>deg,
				#06b6d4 <?= $completed_percent * 3.6 ?>deg <?= ($completed_percent + $progress_percent) * 3.6 ?>deg,
				#f97316 <?= ($completed_percent + $progress_percent) * 3.6 ?>deg 360deg
			);
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
		}

		.chart-circle::before {
			content: '';
			width: 100px;
			height: 100px;
			background: white;
			border-radius: 50%;
			position: absolute;
		}

		.chart-center {
			position: absolute;
			text-align: center;
			z-index: 2;
		}

		.chart-percentage {
			font-size: 24px;
			font-weight: 700;
			color: #2d3748;
		}

		.chart-label {
			font-size: 12px;
			color: #718096;
		}

		.chart-legend {
			display: flex;
			flex-direction: column;
			gap: 15px;
			margin-top: 25px;
		}

		.legend-item {
			display: flex;
			align-items: center;
			gap: 12px;
		}

		.legend-color {
			width: 16px;
			height: 16px;
			border-radius: 4px;
		}

		.legend-color.completed {
			background: #10b981;
		}

		.legend-color.progress {
			background: #06b6d4;
		}

		.legend-color.pending {
			background: #f97316;
		}

		.legend-text {
			flex: 1;
			font-size: 14px;
			color: #4a5568;
			font-weight: 500;
		}

		.legend-value {
			font-size: 14px;
			font-weight: 600;
			color: #2d3748;
		}

		.quick-actions {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			padding: 30px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
		}

		.actions-title {
			font-size: 20px;
			font-weight: 600;
			color: #2d3748;
			margin-bottom: 25px;
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.action-btn {
			display: flex;
			align-items: center;
			gap: 15px;
			padding: 16px 20px;
			background: rgba(102, 126, 234, 0.1);
			border: 1px solid rgba(102, 126, 234, 0.2);
			border-radius: 12px;
			text-decoration: none;
			color: #4a5568;
			font-weight: 500;
			transition: all 0.3s ease;
			margin-bottom: 12px;
		}

		.action-btn:hover {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			transform: translateX(5px);
			box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
		}

		.action-icon {
			width: 40px;
			height: 40px;
			border-radius: 10px;
			background: linear-gradient(135deg, #667eea, #764ba2);
			display: flex;
			align-items: center;
			justify-content: center;
			color: white;
			font-size: 16px;
		}

		.action-btn:hover .action-icon {
			background: rgba(255, 255, 255, 0.2);
		}

		/* Responsive */
		@media (max-width: 1200px) {
			.dashboard-sections {
				grid-template-columns: 1fr;
			}
		}

		@media (max-width: 768px) {
			.section-1 {
				padding: 20px;
			}

			.welcome-header {
				padding: 25px;
			}

			.welcome-content {
				flex-direction: column;
				text-align: center;
				gap: 20px;
			}

			.welcome-text h1 {
				font-size: 28px;
			}

			.dashboard-grid {
				grid-template-columns: 1fr;
				gap: 20px;
			}

			.stat-card {
				padding: 25px;
			}

			.chart-section,
			.quick-actions {
				padding: 25px;
			}
		}

		/* Dark mode support */
		@media (prefers-color-scheme: dark) {
			.section-1 {
				background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
			}

			.welcome-header,
			.stat-card,
			.chart-section,
			.quick-actions {
				background: rgba(45, 55, 72, 0.95);
				border: 1px solid rgba(255, 255, 255, 0.1);
			}

			.welcome-text h1,
			.stat-number,
			.chart-title,
			.actions-title,
			.chart-percentage,
			.legend-value {
				color: #f7fafc;
			}

			.welcome-text p,
			.stat-label,
			.chart-label,
			.legend-text {
				color: #cbd5e0;
			}

			.stat-description {
				color: #a0aec0;
			}

			.chart-circle::before {
				background: #2d3748;
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
			<div class="dashboard-container">
				<!-- Welcome Header -->
				<div class="welcome-header">
					<div class="welcome-content">
						<div class="welcome-text">
							<h1>Welcome back! 👋</h1>
							<p>Here's what's happening with your <?= $_SESSION['role'] == 'admin' ? 'team' : 'tasks' ?> today.</p>
							<div class="role-badge">
								<i class="fas fa-<?= $_SESSION['role'] == 'admin' ? 'crown' : 'user-tie' ?>"></i>
								<?= ucfirst($_SESSION['role']) ?>
							</div>
						</div>
						<div class="welcome-time">
							<div class="current-time" id="currentTime"></div>
							<div class="current-date" id="currentDate"></div>
						</div>
					</div>
				</div>

				<!-- Dashboard Stats -->
				<?php if ($_SESSION['role'] == "admin") { ?>
				<div class="dashboard-grid">
					<div class="stat-card users">
						<div class="stat-header">
							<div class="stat-icon users">
								<i class="fas fa-users"></i>
							</div>
							<div class="stat-trend">+5.2%</div>
						</div>
						<div class="stat-number" data-target="<?= $num_users ?>"><?= $num_users ?></div>
						<div class="stat-label">Total Employee<?= $num_users > 1 ? 's' : '' ?></div>
						<div class="stat-progress">
							<div class="stat-progress-bar users" style="width: <?= min(($num_users / 10) * 100, 100) ?>%"></div>
						</div>
						<div class="stat-description">Active team members</div>
					</div>

					<div class="stat-card tasks">
						<div class="stat-header">
							<div class="stat-icon tasks">
								<i class="fas fa-tasks"></i>
							</div>
							<div class="stat-trend">+12.5%</div>
						</div>
						<div class="stat-number" data-target="<?= $num_task ?>"><?= $num_task ?></div>
						<div class="stat-label">Total Task<?= $num_task > 1 ? 's' : '' ?></div>
						<div class="stat-progress">
							<div class="stat-progress-bar tasks" style="width: <?= min(($num_task / 50) * 100, 100) ?>%"></div>
						</div>
						<div class="stat-description">All assigned tasks</div>
					</div>

					<div class="stat-card due-today">
						<div class="stat-header">
							<div class="stat-icon due-today">
								<i class="fas fa-clock"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $todaydue_task ?>"><?= $todaydue_task ?></div>
						<div class="stat-label">Due Today</div>
						<div class="stat-description">Tasks expiring today</div>
					</div>

					<div class="stat-card overdue">
						<div class="stat-header">
							<div class="stat-icon overdue">
								<i class="fas fa-exclamation-triangle"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $overdue_task ?>"><?= $overdue_task ?></div>
						<div class="stat-label">Overdue</div>
						<div class="stat-description">Past deadline tasks</div>
					</div>

					<div class="stat-card no-deadline">
						<div class="stat-header">
							<div class="stat-icon no-deadline">
								<i class="fas fa-infinity"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $nodeadline_task ?>"><?= $nodeadline_task ?></div>
						<div class="stat-label">No Deadline</div>
						<div class="stat-description">Open-ended tasks</div>
					</div>

					<div class="stat-card pending">
						<div class="stat-header">
							<div class="stat-icon pending">
								<i class="fas fa-hourglass-start"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $pending ?>"><?= $pending ?></div>
						<div class="stat-label">Pending</div>
						<div class="stat-progress">
							<div class="stat-progress-bar pending" style="width: <?= $pending_percent ?>%"></div>
						</div>
						<div class="stat-description"><?= $pending_percent ?>% of total tasks</div>
					</div>

					<div class="stat-card progress">
						<div class="stat-header">
							<div class="stat-icon progress">
								<i class="fas fa-spinner"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $in_progress ?>"><?= $in_progress ?></div>
						<div class="stat-label">In Progress</div>
						<div class="stat-progress">
							<div class="stat-progress-bar progress" style="width: <?= $progress_percent ?>%"></div>
						</div>
						<div class="stat-description"><?= $progress_percent ?>% of total tasks</div>
					</div>

					<div class="stat-card completed">
						<div class="stat-header">
							<div class="stat-icon completed">
								<i class="fas fa-check-circle"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $completed ?>"><?= $completed ?></div>
						<div class="stat-label">Completed</div>
						<div class="stat-progress">
							<div class="stat-progress-bar completed" style="width: <?= $completed_percent ?>%"></div>
						</div>
						<div class="stat-description"><?= $completed_percent ?>% of total tasks</div>
					</div>
				</div>
				<?php } else { ?>
				<div class="dashboard-grid">
					<div class="stat-card tasks">
						<div class="stat-header">
							<div class="stat-icon tasks">
								<i class="fas fa-tasks"></i>
							</div>
							<div class="stat-trend">My Tasks</div>
						</div>
						<div class="stat-number" data-target="<?= $num_my_task ?>"><?= $num_my_task ?></div>
						<div class="stat-label">Total Task<?= $num_my_task > 1 ? 's' : '' ?></div>
						<div class="stat-progress">
							<div class="stat-progress-bar tasks" style="width: <?= min(($num_my_task / 20) * 100, 100) ?>%"></div>
						</div>
						<div class="stat-description">Assigned to me</div>
					</div>

					<div class="stat-card overdue">
						<div class="stat-header">
							<div class="stat-icon overdue">
								<i class="fas fa-exclamation-triangle"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $overdue_task ?>"><?= $overdue_task ?></div>
						<div class="stat-label">Overdue</div>
						<div class="stat-description">Needs immediate attention</div>
					</div>

					<div class="stat-card no-deadline">
						<div class="stat-header">
							<div class="stat-icon no-deadline">
								<i class="fas fa-infinity"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $nodeadline_task ?>"><?= $nodeadline_task ?></div>
						<div class="stat-label">No Deadline</div>
						<div class="stat-description">Flexible timing</div>
					</div>

					<div class="stat-card pending">
						<div class="stat-header">
							<div class="stat-icon pending">
								<i class="fas fa-hourglass-start"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $pending ?>"><?= $pending ?></div>
						<div class="stat-label">Pending</div>
						<div class="stat-progress">
							<div class="stat-progress-bar pending" style="width: <?= $pending_percent ?>%"></div>
						</div>
						<div class="stat-description"><?= $pending_percent ?>% of my tasks</div>
					</div>

					<div class="stat-card progress">
						<div class="stat-header">
							<div class="stat-icon progress">
								<i class="fas fa-spinner"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $in_progress ?>"><?= $in_progress ?></div>
						<div class="stat-label">In Progress</div>
						<div class="stat-progress">
							<div class="stat-progress-bar progress" style="width: <?= $progress_percent ?>%"></div>
						</div>
						<div class="stat-description"><?= $progress_percent ?>% of my tasks</div>
					</div>

					<div class="stat-card completed">
						<div class="stat-header">
							<div class="stat-icon completed">
								<i class="fas fa-check-circle"></i>
							</div>
						</div>
						<div class="stat-number" data-target="<?= $completed ?>"><?= $completed ?></div>
						<div class="stat-label">Completed</div>
						<div class="stat-progress">
							<div class="stat-progress-bar completed" style="width: <?= $completed_percent ?>%"></div>
						</div>
						<div class="stat-description"><?= $completed_percent ?>% of my tasks</div>
					</div>
				</div>
				<?php } ?>

				<!-- Dashboard Sections -->
				<div class="dashboard-sections">
					<div class="chart-section">
						<h3 class="chart-title">
							<i class="fas fa-chart-pie"></i>
							Task Distribution
						</h3>
						<div class="progress-chart">
							<div class="chart-circle">
								<div class="chart-center">
									<div class="chart-percentage"><?= $completed_percent ?>%</div>
									<div class="chart-label">Complete</div>
								</div>
							</div>
						</div>
						<div class="chart-legend">
							<div class="legend-item">
								<div class="legend-color completed"></div>
								<div class="legend-text">Completed</div>
								<div class="legend-value"><?= $completed ?></div>
							</div>
							<div class="legend-item">
								<div class="legend-color progress"></div>
								<div class="legend-text">In Progress</div>
								<div class="legend-value"><?= $in_progress ?></div>
							</div>
							<div class="legend-item">
								<div class="legend-color pending"></div>
								<div class="legend-text">Pending</div>
								<div class="legend-value"><?= $pending ?></div>
							</div>
						</div>
					</div>

					<div class="quick-actions">
						<h3 class="actions-title">
							<i class="fas fa-bolt"></i>
							Quick Actions
						</h3>
						<?php if ($_SESSION['role'] == "admin") { ?>
						<a href="add-task.php" class="action-btn">
							<div class="action-icon">
								<i class="fas fa-plus"></i>
							</div>
							<div>
								<div style="font-weight: 600;">Add New Task</div>
								<div style="font-size: 12px; color: #a0aec0;">Create and assign tasks</div>
							</div>
						</a>
						<a href="tasks.php" class="action-btn">
							<div class="action-icon">
								<i class="fas fa-list"></i>
							</div>
							<div>
								<div style="font-weight: 600;">View All Tasks</div>
								<div style="font-size: 12px; color: #a0aec0;">Manage existing tasks</div>
							</div>
						</a>
						<a href="employees.php" class="action-btn">
							<div class="action-icon">
								<i class="fas fa-users"></i>
							</div>
							<div>
								<div style="font-weight: 600;">Manage Employees</div>
								<div style="font-size: 12px; color: #a0aec0;">View team members</div>
							</div>
						</a>
						<?php } else { ?>
						<a href="my-tasks.php" class="action-btn">
							<div class="action-icon">
								<i class="fas fa-tasks"></i>
							</div>
							<div>
								<div style="font-weight: 600;">My Tasks</div>
								<div style="font-size: 12px; color: #a0aec0;">View assigned tasks</div>
							</div>
						</a>
						<a href="profile.php" class="action-btn">
							<div class="action-icon">
								<i class="fas fa-user"></i>
							</div>
							<div>
								<div style="font-weight: 600;">My Profile</div>
								<div style="font-size: 12px; color: #a0aec0;">Update your information</div>
							</div>
						</a>
						<a href="notifications.php" class="action-btn">
							<div class="action-icon">
								<i class="fas fa-bell"></i>
							</div>
							<div>
								<div style="font-weight: 600;">Notifications</div>
								<div style="font-size: 12px; color: #a0aec0;">Check latest updates</div>
							</div>
						</a>
						<?php } ?>
					</div>
				</div>
			</div>
		</section>
	</div>

	<script>
		// Highlight active navigation
		document.querySelector("#navList li:nth-child(1)").classList.add("active");

		// Update time and date
		function updateDateTime() {
			const now = new Date();
			const timeOptions = { 
				hour: '2-digit', 
				minute: '2-digit',
				hour12: true 
			};
			const dateOptions = { 
				weekday: 'long', 
				year: 'numeric', 
				month: 'long', 
				day: 'numeric' 
			};

			document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', timeOptions);
			document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', dateOptions);
		}

		// Update time every second
		updateDateTime();
		setInterval(updateDateTime, 1000);

		// Animate numbers on load
		document.addEventListener('DOMContentLoaded', function() {
			const numberElements = document.querySelectorAll('[data-target]');
			
			numberElements.forEach(element => {
				const target = parseInt(element.getAttribute('data-target'));
				let current = 0;
				const increment = target / 50;
				
				const timer = setInterval(() => {
					current += increment;
					if (current >= target) {
						element.textContent = target;
						clearInterval(timer);
					} else {
						element.textContent = Math.floor(current);
					}
				}, 30);
			});

			// SYSTÈME DE NOTIFICATIONS SIMPLIFIÉ
			// Fonction pour mettre à jour le compteur de notifications
			function updateNotificationCountDisplay(count) {
				const badge = $("#notificationNum");
				
				if (count > 0) {
					badge.text(count).addClass('show');
				} else {
					badge.removeClass('show').text('');
				}
			}

			// Écouter les événements de mise à jour du compteur
			window.addEventListener('updateNotificationCount', function(event) {
				const count = event.detail.count;
				updateNotificationCountDisplay(count);
			});

			// Fonction pour rafraîchir le compteur depuis le serveur
			function refreshNotificationCount() {
				fetch('app/notification-count.php')
					.then(response => response.text())
					.then(count => {
						const intCount = parseInt(count) || 0;
						updateNotificationCountDisplay(intCount);
					})
					.catch(error => {
						console.error('Erreur lors du rafraîchissement du compteur:', error);
					});
			}

			// Rafraîchir le compteur au chargement et périodiquement
			setTimeout(refreshNotificationCount, 1000);
			setInterval(refreshNotificationCount, 30000); // toutes les 30 secondes
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