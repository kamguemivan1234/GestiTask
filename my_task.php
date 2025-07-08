<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";

    $tasks = get_all_tasks_by_id($conn, $_SESSION['id']);

    function get_file_icon($filename) {
        if (!$filename) return '';
        
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $icons = [
            'pdf' => 'fas fa-file-pdf text-red-500',
            'doc' => 'fas fa-file-word text-blue-500',
            'docx' => 'fas fa-file-word text-blue-500',
            'xls' => 'fas fa-file-excel text-green-500',
            'xlsx' => 'fas fa-file-excel text-green-500',
            'ppt' => 'fas fa-file-powerpoint text-orange-500',
            'pptx' => 'fas fa-file-powerpoint text-orange-500',
            'jpg' => 'fas fa-file-image text-purple-500',
            'jpeg' => 'fas fa-file-image text-purple-500',
            'png' => 'fas fa-file-image text-purple-500',
            'gif' => 'fas fa-file-image text-purple-500',
            'txt' => 'fas fa-file-alt text-gray-500',
            'zip' => 'fas fa-file-archive text-yellow-500',
            'rar' => 'fas fa-file-archive text-yellow-500'
        ];
        
        return $icons[$extension] ?? 'fas fa-file text-gray-500';
    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>My Tasks</title>
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

		.tasks-container {
			display: grid;
			gap: 20px;
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

		.task-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			padding: 25px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
			transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
			position: relative;
			overflow: hidden;
		}

		.task-card::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #667eea, #764ba2);
			opacity: 0;
			transition: opacity 0.3s ease;
		}

		.task-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
		}

		.task-card:hover::before {
			opacity: 1;
		}

		.task-header {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			margin-bottom: 20px;
		}

		.task-number {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			width: 35px;
			height: 35px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 600;
			font-size: 14px;
			box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
		}

		.task-title {
			font-size: 20px;
			font-weight: 600;
			color: #2d3748;
			margin: 0 0 8px 0;
			line-height: 1.3;
			flex: 1;
			margin-left: 15px;
		}

		.task-description {
			color: #718096;
			font-size: 15px;
			line-height: 1.5;
			margin-bottom: 20px;
		}

		.task-meta {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 20px;
			margin-bottom: 25px;
		}

		.meta-item {
			display: flex;
			align-items: center;
			gap: 10px;
		}

		.meta-icon {
			width: 40px;
			height: 40px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: 16px;
		}

		.due-date-icon {
			background: rgba(255, 107, 107, 0.1);
			color: #ff6b6b;
		}

		.status-icon {
			background: rgba(102, 126, 234, 0.1);
			color: #667eea;
		}

		.meta-text {
			flex: 1;
		}

		.meta-label {
			font-size: 12px;
			color: #a0aec0;
			font-weight: 500;
			margin-bottom: 2px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.meta-value {
			font-size: 14px;
			font-weight: 600;
			color: #4a5568;
		}

		.status-badge {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			padding: 8px 16px;
			border-radius: 12px;
			font-size: 13px;
			font-weight: 600;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}

		.status-completed {
			background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
			color: #2f855a;
			border: 1px solid #68d391;
		}

		.status-pending {
			background: linear-gradient(135deg, #fed7a1, #fbb040);
			color: #c05621;
			border: 1px solid #f6ad55;
		}

		.status-progress {
			background: linear-gradient(135deg, #bee3f8, #90cdf4);
			color: #2c5282;
			border: 1px solid #63b3ed;
		}

		.attachment-section {
			background: rgba(102, 126, 234, 0.05);
			border: 1px solid rgba(102, 126, 234, 0.2);
			border-radius: 12px;
			padding: 15px;
			margin-bottom: 20px;
		}

		.attachment-item {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 8px 0;
		}

		.attachment-icon {
			font-size: 18px;
		}

		.attachment-name {
			flex: 1;
			font-size: 14px;
			color: #4a5568;
			font-weight: 500;
		}

		.download-btn {
			background: linear-gradient(135deg, #48bb78, #38a169);
			color: white;
			padding: 6px 12px;
			border-radius: 8px;
			text-decoration: none;
			font-size: 12px;
			font-weight: 600;
			display: inline-flex;
			align-items: center;
			gap: 6px;
			transition: all 0.3s ease;
		}

		.download-btn:hover {
			transform: translateY(-1px);
			box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
			color: white;
			text-decoration: none;
		}

		.task-actions {
			display: flex;
			justify-content: flex-end;
			padding-top: 20px;
			border-top: 1px solid #e2e8f0;
		}

		.edit-btn {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
			padding: 12px 24px;
			border-radius: 12px;
			text-decoration: none;
			font-size: 14px;
			font-weight: 600;
			display: inline-flex;
			align-items: center;
			gap: 8px;
			transition: all 0.3s ease;
			border: none;
			cursor: pointer;
			box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
		}

		.edit-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
			color: white;
			text-decoration: none;
		}

		.empty-state {
			text-align: center;
			padding: 80px 40px;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 20px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
		}

		.empty-state-icon {
			width: 80px;
			height: 80px;
			background: linear-gradient(135deg, #e2e8f0, #cbd5e0);
			border-radius: 20px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 20px;
			font-size: 32px;
			color: #a0aec0;
		}

		.empty-state h3 {
			color: #4a5568;
			font-size: 24px;
			font-weight: 600;
			margin-bottom: 10px;
		}

		.empty-state p {
			color: #718096;
			font-size: 16px;
			margin: 0;
		}

		.stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
			gap: 20px;
			margin-bottom: 30px;
		}

		.stat-card {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 16px;
			padding: 20px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
			text-align: center;
		}

		.stat-icon {
			width: 50px;
			height: 50px;
			border-radius: 12px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 15px;
			font-size: 20px;
		}

		.stat-total {
			background: linear-gradient(135deg, #667eea, #764ba2);
			color: white;
		}

		.stat-completed {
			background: linear-gradient(135deg, #48bb78, #38a169);
			color: white;
		}

		.stat-pending {
			background: linear-gradient(135deg, #ed8936, #dd6b20);
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

		/* Responsive Design */
		@media (max-width: 768px) {
			.section-1 {
				padding: 20px;
			}

			.page-header {
				padding: 20px;
			}

			.page-title {
				font-size: 24px;
			}

			.task-meta {
				grid-template-columns: 1fr;
				gap: 15px;
			}

			.task-card {
				padding: 20px;
			}

			.task-header {
				flex-direction: column;
				gap: 15px;
			}

			.task-title {
				margin-left: 0;
			}
		}

		/* Dark mode support */
		@media (prefers-color-scheme: dark) {
			.section-1 {
				background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
			}

			.page-header,
			.task-card,
			.empty-state,
			.stat-card {
				background: rgba(45, 55, 72, 0.95);
				border: 1px solid rgba(255, 255, 255, 0.1);
			}

			.page-title,
			.task-title,
			.stat-number {
				color: #f7fafc;
			}

			.page-subtitle,
			.task-description,
			.meta-value,
			.stat-label,
			.attachment-name {
				color: #cbd5e0;
			}

			.meta-label {
				color: #a0aec0;
			}

			.attachment-section {
				background: rgba(102, 126, 234, 0.1);
				border-color: rgba(102, 126, 234, 0.3);
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
					<i class="fas fa-tasks"></i>
					My Tasks
				</h1>
				<p class="page-subtitle">Manage and track your assigned tasks</p>
			</div>

			<?php if (isset($_GET['success'])) { ?>
				<div class="success-alert">
					<i class="fas fa-check-circle"></i>
					<?= stripcslashes($_GET['success']); ?>
				</div>
			<?php } ?>

			<?php if ($tasks != 0) { ?>
				<?php 
				$total_tasks = count($tasks);
				$completed_tasks = 0;
				$pending_tasks = 0;
				
				foreach ($tasks as $task) {
					if ($task['status'] == 'completed') {
						$completed_tasks++;
					} else {
						$pending_tasks++;
					}
				}
				?>

				<div class="stats-grid">
					<div class="stat-card">
						<div class="stat-icon stat-total">
							<i class="fas fa-list-check"></i>
						</div>
						<div class="stat-number"><?= $total_tasks ?></div>
						<div class="stat-label">Total Tasks</div>
					</div>
					<div class="stat-card">
						<div class="stat-icon stat-completed">
							<i class="fas fa-check-circle"></i>
						</div>
						<div class="stat-number"><?= $completed_tasks ?></div>
						<div class="stat-label">Completed</div>
					</div>
					<div class="stat-card">
						<div class="stat-icon stat-pending">
							<i class="fas fa-clock"></i>
						</div>
						<div class="stat-number"><?= $pending_tasks ?></div>
						<div class="stat-label">In Progress</div>
					</div>
				</div>

				<div class="tasks-container">
					<?php $i = 0; foreach ($tasks as $task) { ?>
					<div class="task-card">
						<div class="task-header">
							<div class="task-number"><?= ++$i ?></div>
							<h3 class="task-title"><?= htmlspecialchars($task['title']) ?></h3>
						</div>
						
						<div class="task-description">
							<?= htmlspecialchars($task['description']) ?>
						</div>

						<?php if ($task['attachment']) { ?>
						<div class="attachment-section">
							<div class="attachment-item">
								<i class="<?= get_file_icon($task['attachment']) ?> attachment-icon"></i>
								<span class="attachment-name"><?= htmlspecialchars($task['attachment']) ?></span>
								<a href="download.php?file=<?= urlencode($task['attachment']) ?>" class="download-btn">
									<i class="fas fa-download"></i>
									Download
								</a>
							</div>
						</div>
						<?php } ?>

						<div class="task-meta">
							<div class="meta-item">
								<div class="meta-icon due-date-icon">
									<i class="fas fa-calendar-alt"></i>
								</div>
								<div class="meta-text">
									<div class="meta-label">Due Date</div>
									<div class="meta-value">
										<?= $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : 'No deadline' ?>
									</div>
								</div>
							</div>
							<div class="meta-item">
								<div class="meta-icon status-icon">
									<i class="fas fa-info-circle"></i>
								</div>
								<div class="meta-text">
									<div class="meta-label">Status</div>
									<div class="meta-value">
										<?php 
										$status_class = 'status-pending';
										if ($task['status'] == 'completed') {
											$status_class = 'status-completed';
										} elseif ($task['status'] == 'in_progress') {
											$status_class = 'status-progress';
										}
										?>
										<span class="status-badge <?= $status_class ?>">
											<?php if ($task['status'] == 'completed') { ?>
												<i class="fas fa-check"></i>
											<?php } elseif ($task['status'] == 'in_progress') { ?>
												<i class="fas fa-clock"></i>
											<?php } else { ?>
												<i class="fas fa-pause"></i>
											<?php } ?>
											<?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="task-actions">
							<a href="edit-task-employee.php?id=<?= $task['id'] ?>" class="edit-btn">
								<i class="fas fa-edit"></i>
								Edit Task
							</a>
						</div>
					</div>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div class="empty-state">
					<div class="empty-state-icon">
						<i class="fas fa-tasks"></i>
					</div>
					<h3>No Tasks Yet</h3>
					<p>You don't have any tasks assigned at the moment. Check back later for new assignments.</p>
				</div>
			<?php } ?>
		</section>
	</div>

<script>
	// Highlight the "My Task" tab in sidebar
	document.querySelector("#navList li:nth-child(2)").classList.add("active");

	// Add loading animation to edit buttons
	document.querySelectorAll('.edit-btn').forEach(btn => {
		btn.addEventListener('click', function() {
			this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
		});
	});

	// Support du mode sombre
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