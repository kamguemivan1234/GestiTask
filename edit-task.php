<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    
    if (!isset($_GET['id'])) {
    	 header("Location: tasks.php");
    	 exit();
    }
    $id = $_GET['id'];
    $task = get_task_by_id($conn, $id);

    if ($task == 0) {
    	 header("Location: tasks.php");
    	 exit();
    }
   $users = get_all_users($conn);
 ?>
<!DOCTYPE html>
<html>
<head>
	<title>Edit Task</title>
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

		.breadcrumb {
			display: flex;
			align-items: center;
			gap: 10px;
			color: #718096;
		}

		.breadcrumb a {
			color: #667eea;
			text-decoration: none;
			font-weight: 500;
			transition: color 0.3s ease;
		}

		.breadcrumb a:hover {
			color: #764ba2;
		}

		.breadcrumb i {
			font-size: 12px;
		}

		.form-container {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 24px;
			padding: 40px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
			border: 1px solid rgba(255, 255, 255, 0.2);
			max-width: 800px;
			animation: slideUp 0.8s ease-out;
		}

		@keyframes slideUp {
			from {
				opacity: 0;
				transform: translateY(30px);
			}
			to {
				opacity: 1;
				transform: translateY(0);
			}
		}

		.form-container::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
			border-radius: 24px 24px 0 0;
		}

		.alert {
			border: none;
			border-radius: 16px;
			padding: 20px 25px;
			margin-bottom: 25px;
			font-weight: 500;
			animation: slideDown 0.5s ease-out;
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

		.alert-danger {
			background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
			color: #c53030;
			border-left: 4px solid #e53e3e;
		}

		.alert-success {
			background: linear-gradient(135deg, #c6f6d5 0%, #9ae6b4 100%);
			color: #2f855a;
			border-left: 4px solid #38a169;
		}

		.form-grid {
			display: grid;
			gap: 25px;
		}

		.form-group {
			position: relative;
		}

		.form-group.full-width {
			grid-column: 1 / -1;
		}

		.form-label {
			font-size: 14px;
			font-weight: 600;
			color: #4a5568;
			margin-bottom: 8px;
			display: block;
			letter-spacing: 0.5px;
		}

		.form-input {
			width: 100%;
			padding: 16px 20px;
			border: 2px solid #e2e8f0;
			border-radius: 16px;
			font-size: 16px;
			font-weight: 500;
			background: rgba(255, 255, 255, 0.8);
			transition: all 0.3s ease;
			font-family: 'Inter', sans-serif;
		}

		.form-input:focus {
			outline: none;
			border-color: #667eea;
			box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
			background: rgba(255, 255, 255, 1);
			transform: translateY(-2px);
		}

		.form-input::placeholder {
			color: #a0aec0;
		}

		.form-textarea {
			min-height: 120px;
			resize: vertical;
		}

		.form-select {
			appearance: none;
			background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
			background-position: right 16px center;
			background-repeat: no-repeat;
			background-size: 16px;
			padding-right: 50px;
			cursor: pointer;
		}

		.form-date {
			position: relative;
		}

		.form-date::before {
			content: '\f073';
			font-family: 'Font Awesome 6 Free';
			font-weight: 900;
			position: absolute;
			right: 20px;
			top: 50%;
			transform: translateY(-50%);
			color: #a0aec0;
			pointer-events: none;
			z-index: 1;
		}

		.input-icon {
			position: absolute;
			left: 20px;
			top: 50%;
			transform: translateY(-50%);
			color: #a0aec0;
			font-size: 18px;
			transition: color 0.3s ease;
		}

		.form-input:focus + .input-icon {
			color: #667eea;
		}

		.form-input.with-icon {
			padding-left: 55px;
		}

		.form-actions {
			display: flex;
			gap: 15px;
			justify-content: flex-end;
			margin-top: 30px;
			padding-top: 25px;
			border-top: 1px solid #e2e8f0;
		}

		.btn {
			padding: 16px 32px;
			border-radius: 16px;
			font-size: 16px;
			font-weight: 600;
			letter-spacing: 0.5px;
			cursor: pointer;
			transition: all 0.3s ease;
			border: none;
			display: inline-flex;
			align-items: center;
			gap: 10px;
			text-decoration: none;
		}

		.btn-primary {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
		}

		.btn-primary:hover {
			transform: translateY(-3px);
			box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
		}

		.btn-secondary {
			background: rgba(113, 128, 150, 0.1);
			color: #718096;
			border: 2px solid #e2e8f0;
		}

		.btn-secondary:hover {
			background: rgba(113, 128, 150, 0.2);
			border-color: #cbd5e0;
			transform: translateY(-2px);
		}

		.form-help {
			font-size: 13px;
			color: #a0aec0;
			margin-top: 6px;
			font-style: italic;
		}

		.required {
			color: #e53e3e;
		}

		/* Animation pour les champs */
		.form-group {
			animation: fadeInUp 0.6s ease-out;
			animation-fill-mode: both;
		}

		.form-group:nth-child(1) { animation-delay: 0.1s; }
		.form-group:nth-child(2) { animation-delay: 0.2s; }
		.form-group:nth-child(3) { animation-delay: 0.3s; }
		.form-group:nth-child(4) { animation-delay: 0.4s; }

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

		/* États de validation */
		.form-input.valid {
			border-color: #48bb78;
			background: rgba(72, 187, 120, 0.05);
		}

		.form-input.invalid {
			border-color: #e53e3e;
			background: rgba(229, 62, 62, 0.05);
		}

		/* Responsive */
		@media (min-width: 768px) {
			.form-grid {
				grid-template-columns: 1fr 1fr;
			}
		}

		@media (max-width: 768px) {
			.section-1 {
				padding: 20px;
			}

			.form-container {
				padding: 30px 25px;
			}

			.page-header {
				padding: 25px;
				flex-direction: column;
				gap: 15px;
				text-align: center;
			}

			.page-title {
				font-size: 24px;
			}

			.form-actions {
				flex-direction: column;
			}

			.btn {
				justify-content: center;
			}
		}

		/* Dark mode support */
		@media (prefers-color-scheme: dark) {
			.section-1 {
				background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
			}

			.page-header,
			.form-container {
				background: rgba(45, 55, 72, 0.95);
				border: 1px solid rgba(255, 255, 255, 0.1);
			}

			.page-title,
			.form-label {
				color: #f7fafc;
			}

			.breadcrumb {
				color: #cbd5e0;
			}

			.form-input {
				background: rgba(74, 85, 104, 0.6);
				border-color: #4a5568;
				color: #f7fafc;
			}

			.form-input::placeholder {
				color: #a0aec0;
			}

			.form-help {
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
					<i class="fas fa-edit"></i>
					Edit Task
				</h1>
				<div class="breadcrumb">
					<a href="tasks.php">
						<i class="fas fa-tasks"></i>
						Tasks
					</a>
					<i class="fas fa-chevron-right"></i>
					<span>Edit</span>
				</div>
			</div>

			<div class="form-container">
				<?php if (isset($_GET['error'])) {?>
					<div class="alert alert-danger">
						<i class="fas fa-exclamation-triangle"></i>
						<?php echo stripcslashes($_GET['error']); ?>
					</div>
				<?php } ?>

				<?php if (isset($_GET['success'])) {?>
					<div class="alert alert-success">
						<i class="fas fa-check-circle"></i>
						<?php echo stripcslashes($_GET['success']); ?>
					</div>
				<?php } ?>

				<form method="POST" action="app/update-task.php" id="editTaskForm">
					<div class="form-grid">
						<div class="form-group full-width">
							<label class="form-label">
								<i class="fas fa-heading"></i>
								Task Title <span class="required">*</span>
							</label>
							<input 
								type="text" 
								name="title" 
								class="form-input" 
								placeholder="Enter task title" 
								value="<?= htmlspecialchars($task['title']) ?>"
								required
							>
							<div class="form-help">A clear and descriptive title for the task</div>
						</div>

						<div class="form-group full-width">
							<label class="form-label">
								<i class="fas fa-align-left"></i>
								Description <span class="required">*</span>
							</label>
							<textarea 
								name="description" 
								class="form-input form-textarea" 
								placeholder="Describe the task in detail..."
								required
							><?= htmlspecialchars($task['description']) ?></textarea>
							<div class="form-help">Provide detailed instructions and requirements</div>
						</div>

						<div class="form-group">
							<label class="form-label">
								<i class="fas fa-calendar-alt"></i>
								Due Date <span class="required">*</span>
							</label>
							<div class="form-date">
								<input 
									type="date" 
									name="due_date" 
									class="form-input" 
									value="<?= $task['due_date'] ?>"
									required
								>
							</div>
							<div class="form-help">When should this task be completed?</div>
						</div>

						<div class="form-group">
							<label class="form-label">
								<i class="fas fa-user-plus"></i>
								Assign To <span class="required">*</span>
							</label>
							<select name="assigned_to" class="form-input form-select" required>
								<option value="">Select an employee</option>
								<?php if ($users != 0) { 
									foreach ($users as $user) {
										$selected = ($task['assigned_to'] == $user['id']) ? 'selected' : '';
								?>
									<option value="<?= $user['id'] ?>" <?= $selected ?>>
										<?= htmlspecialchars($user['full_name']) ?>
									</option>
								<?php } } ?>
							</select>
							<div class="form-help">Choose who will be responsible for this task</div>
						</div>
					</div>

					<input type="hidden" name="id" value="<?= $task['id'] ?>">

					<div class="form-actions">
						<a href="tasks.php" class="btn btn-secondary">
							<i class="fas fa-times"></i>
							Cancel
						</a>
						<button type="submit" class="btn btn-primary" id="submitBtn">
							<i class="fas fa-save"></i>
							Update Task
						</button>
					</div>
				</form>
			</div>
		</section>
	</div>

	<script>
		// Highlight the active nav item
		document.querySelector("#navList li:nth-child(4)").classList.add("active");

		// Form validation and enhancement
		document.addEventListener('DOMContentLoaded', function() {
			const form = document.getElementById('editTaskForm');
			const inputs = form.querySelectorAll('.form-input');
			const submitBtn = document.getElementById('submitBtn');

			// Add real-time validation
			inputs.forEach(input => {
				input.addEventListener('blur', function() {
					validateField(this);
				});

				input.addEventListener('input', function() {
					if (this.classList.contains('invalid')) {
						validateField(this);
					}
				});
			});

			function validateField(field) {
				const value = field.value.trim();
				
				if (field.hasAttribute('required') && !value) {
					field.classList.add('invalid');
					field.classList.remove('valid');
				} else if (value) {
					field.classList.add('valid');
					field.classList.remove('invalid');
				} else {
					field.classList.remove('valid', 'invalid');
				}
			}

			// Form submission with loading state
			form.addEventListener('submit', function(e) {
				submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
				submitBtn.disabled = true;
			});

			// Auto-resize textarea
			const textarea = form.querySelector('textarea');
			if (textarea) {
				textarea.addEventListener('input', function() {
					this.style.height = 'auto';
					this.style.height = this.scrollHeight + 'px';
				});
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
<?php }else{ 
   $em = "First login";
   header("Location: login.php?error=$em");
   exit();
}
?>