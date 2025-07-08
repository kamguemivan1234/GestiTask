<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login | Task Management System</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/style.css">
	<style>
		@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
		
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		
		body {
			font-family: 'Inter', sans-serif;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			position: relative;
			overflow-x: hidden;
		}
		
		body::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 1000"><defs><radialGradient id="a" cx="50%" cy="50%"><stop offset="0%" stop-color="%23ffffff" stop-opacity="0.1"/><stop offset="100%" stop-color="%23ffffff" stop-opacity="0"/></radialGradient></defs><circle cx="200" cy="200" r="100" fill="url(%23a)"/><circle cx="800" cy="300" r="150" fill="url(%23a)"/><circle cx="400" cy="700" r="120" fill="url(%23a)"/><circle cx="900" cy="800" r="80" fill="url(%23a)"/></svg>') no-repeat center center;
			background-size: cover;
			pointer-events: none;
		}
		
		.login-body {
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
			padding: 20px;
			position: relative;
			z-index: 1;
		}
		
		.login-container {
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border: 1px solid rgba(255, 255, 255, 0.2);
			border-radius: 24px;
			padding: 50px 40px;
			width: 100%;
			max-width: 450px;
			box-shadow: 
				0 20px 40px rgba(0, 0, 0, 0.1),
				0 15px 25px rgba(0, 0, 0, 0.05);
			position: relative;
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
		
		.login-container::before {
			content: '';
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			height: 4px;
			background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
			border-radius: 24px 24px 0 0;
		}
		
		.login-header {
			text-align: center;
			margin-bottom: 40px;
		}
		
		.login-icon {
			width: 80px;
			height: 80px;
			background: linear-gradient(135deg, #667eea, #764ba2);
			border-radius: 20px;
			display: flex;
			align-items: center;
			justify-content: center;
			margin: 0 auto 20px;
			box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
			animation: pulse 2s infinite;
		}
		
		@keyframes pulse {
			0%, 100% {
				transform: scale(1);
			}
			50% {
				transform: scale(1.05);
			}
		}
		
		.login-icon i {
			font-size: 32px;
			color: white;
		}
		
		.login-title {
			font-size: 32px;
			font-weight: 700;
			color: #2d3748;
			margin-bottom: 8px;
			letter-spacing: -0.5px;
		}
		
		.login-subtitle {
			font-size: 16px;
			color: #718096;
			font-weight: 400;
		}
		
		.form-floating {
			margin-bottom: 24px;
			position: relative;
		}
		
		.form-floating .form-control {
			height: 60px;
			border: 2px solid #e2e8f0;
			border-radius: 16px;
			padding: 20px 16px 8px;
			font-size: 16px;
			font-weight: 500;
			background: rgba(255, 255, 255, 0.8);
			transition: all 0.3s ease;
		}
		
		.form-floating .form-control:focus {
			border-color: #667eea;
			box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
			background: rgba(255, 255, 255, 1);
			transform: translateY(-2px);
		}
		
		.form-floating label {
			color: #718096;
			font-weight: 500;
			padding: 16px;
		}
		
		.form-floating .form-control:focus ~ label,
		.form-floating .form-control:not(:placeholder-shown) ~ label {
			color: #667eea;
			transform: scale(0.85) translateY(-24px) translateX(-16px);
		}
		
		.btn-login {
			width: 100%;
			height: 60px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			border-radius: 16px;
			color: white;
			font-size: 16px;
			font-weight: 600;
			letter-spacing: 0.5px;
			transition: all 0.3s ease;
			position: relative;
			overflow: hidden;
			margin-top: 20px;
		}
		
		.btn-login::before {
			content: '';
			position: absolute;
			top: 0;
			left: -100%;
			width: 100%;
			height: 100%;
			background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
			transition: left 0.5s ease;
		}
		
		.btn-login:hover::before {
			left: 100%;
		}
		
		.btn-login:hover {
			transform: translateY(-2px);
			box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
		}
		
		.btn-login:active {
			transform: translateY(0);
		}
		
		.alert {
			border: none;
			border-radius: 12px;
			padding: 16px 20px;
			margin-bottom: 24px;
			font-weight: 500;
			animation: slideDown 0.5s ease-out;
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
		
		.form-footer {
			text-align: center;
			margin-top: 30px;
			padding-top: 24px;
			border-top: 1px solid #e2e8f0;
		}
		
		.form-footer p {
			color: #718096;
			font-size: 14px;
			margin: 0;
		}
		
		/* Animations pour les inputs */
		.input-group {
			position: relative;
		}
		
		.input-icon {
			position: absolute;
			left: 16px;
			top: 50%;
			transform: translateY(-50%);
			color: #a0aec0;
			font-size: 18px;
			z-index: 2;
			transition: color 0.3s ease;
		}
		
		.form-control:focus + .input-icon {
			color: #667eea;
		}
		
		.form-control.with-icon {
			padding-left: 50px;
		}
		
		/* Responsive */
		@media (max-width: 576px) {
			.login-container {
				padding: 40px 30px;
				margin: 10px;
			}
			
			.login-title {
				font-size: 28px;
			}
		}
		
		/* Dark mode support */
		@media (prefers-color-scheme: dark) {
			.login-container {
				background: rgba(26, 32, 44, 0.95);
				border: 1px solid rgba(255, 255, 255, 0.1);
			}
			
			.login-title {
				color: #f7fafc;
			}
			
			.login-subtitle {
				color: #cbd5e0;
			}
			
			.form-floating .form-control {
				background: rgba(45, 55, 72, 0.6);
				border-color: #4a5568;
				color: #f7fafc;
			}
			
			.form-floating label {
				color: #cbd5e0;
			}
			
			.form-footer p {
				color: #cbd5e0;
			}
		}
	</style>
</head>
<body class="login-body">
	<div class="login-container">
		<div class="login-header">
			<div class="login-icon">
				<i class="fas fa-user-shield"></i>
			</div>
			<h1 class="login-title">Welcome Back</h1>
			<p class="login-subtitle">Please sign in to your account</p>
		</div>

		<form method="POST" action="app/login.php">
			<?php if (isset($_GET['error'])) {?>
				<div class="alert alert-danger" role="alert">
					<i class="fas fa-exclamation-triangle me-2"></i>
					<?php echo stripcslashes($_GET['error']); ?>
				</div>
			<?php } ?>

			<?php if (isset($_GET['success'])) {?>
				<div class="alert alert-success" role="alert">
					<i class="fas fa-check-circle me-2"></i>
					<?php echo stripcslashes($_GET['success']); ?>
				</div>
			<?php } ?>

			<div class="form-floating">
				<input type="text" class="form-control" name="user_name" id="username" placeholder="Username" required>
				<label for="username">
					<i class="fas fa-user me-2"></i>Username
				</label>
			</div>

			<div class="form-floating">
				<input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
				<label for="password">
					<i class="fas fa-lock me-2"></i>Password
				</label>
			</div>

			<button type="submit" class="btn btn-login">
				<i class="fas fa-sign-in-alt me-2"></i>
				Sign In
			</button>
		</form>

		<div class="form-footer">
			<p>&copy; 2024 Task Management System. All rights reserved.</p>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	
	<script>
		// Animation pour les champs de saisie
		document.addEventListener('DOMContentLoaded', function() {
			const inputs = document.querySelectorAll('.form-control');
			
			inputs.forEach(input => {
				input.addEventListener('focus', function() {
					this.parentElement.classList.add('focused');
				});
				
				input.addEventListener('blur', function() {
					if (this.value === '') {
						this.parentElement.classList.remove('focused');
					}
				});
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