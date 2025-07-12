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
		
		.btn-login:disabled {
			background: #a0aec0;
			cursor: not-allowed;
			transform: none;
			box-shadow: none;
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
		
		.btn-login:hover:not(:disabled)::before {
			left: 100%;
		}
		
		.btn-login:hover:not(:disabled) {
			transform: translateY(-2px);
			box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
		}
		
		.btn-login:active:not(:disabled) {
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
		
		.alert-warning {
			background: linear-gradient(135deg, #fef5e7 0%, #fed7aa 100%);
			color: #c05621;
			border-left: 4px solid #ed8936;
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
		
		/* Security Features Styles */
		.captcha-section {
			background: rgba(248, 250, 252, 0.8);
			border: 2px dashed #cbd5e0;
			border-radius: 16px;
			padding: 20px;
			margin: 24px 0;
			animation: slideIn 0.5s ease-out;
		}
		
		@keyframes slideIn {
			from {
				opacity: 0;
				transform: scale(0.95);
			}
			to {
				opacity: 1;
				transform: scale(1);
			}
		}
		
		.captcha-header {
			display: flex;
			align-items: center;
			color: #2d3748;
			font-weight: 600;
			margin-bottom: 16px;
			font-size: 14px;
		}
		
		.captcha-box {
			display: flex;
			align-items: center;
			gap: 12px;
			margin-bottom: 16px;
		}
		
		.captcha-display {
			background: linear-gradient(45deg, #f0f0f0, #e0e0e0);
			border: 2px solid #cbd5e0;
			border-radius: 12px;
			padding: 16px 24px;
			font-family: 'Courier New', monospace;
			font-size: 24px;
			font-weight: bold;
			letter-spacing: 4px;
			text-align: center;
			color: #2d3748;
			user-select: none;
			min-width: 120px;
			text-decoration: line-through;
			text-decoration-color: rgba(45, 55, 72, 0.3);
		}
		
		.captcha-refresh {
			background: #667eea;
			border: none;
			border-radius: 8px;
			color: white;
			width: 40px;
			height: 40px;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.3s ease;
		}
		
		.captcha-refresh:hover {
			background: #5a67d8;
			transform: rotate(180deg);
		}
		
		.security-status {
			background: linear-gradient(135deg, #fff5f5 0%, #fed7d7 100%);
			border: 2px solid #feb2b2;
			border-radius: 16px;
			padding: 16px 20px;
			margin: 24px 0;
			animation: pulse 2s infinite;
		}
		
		.security-info {
			display: flex;
			align-items: center;
			color: #c53030;
			font-weight: 600;
			margin-bottom: 8px;
		}
		
		.security-info i {
			margin-right: 8px;
			font-size: 18px;
		}
		
		.security-timer {
			font-family: 'Courier New', monospace;
			font-size: 18px;
			font-weight: bold;
			color: #e53e3e;
			text-align: center;
		}
		
		.security-log {
			background: rgba(237, 242, 247, 0.8);
			border-radius: 12px;
			padding: 16px;
			margin-top: 24px;
			max-height: 200px;
			overflow-y: auto;
		}
		
		.security-log h6 {
			color: #2d3748;
			margin-bottom: 12px;
			font-weight: 600;
		}
		
		.log-entry {
			font-size: 12px;
			color: #4a5568;
			margin-bottom: 4px;
			padding: 4px 8px;
			border-radius: 6px;
			background: rgba(255, 255, 255, 0.6);
		}
		
		.log-entry.warning {
			background: rgba(255, 235, 153, 0.6);
			color: #744210;
		}
		
		.log-entry.error {
			background: rgba(254, 178, 178, 0.6);
			color: #742a2a;
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
			
			.captcha-display {
				font-size: 20px;
				letter-spacing: 2px;
				padding: 12px 16px;
				min-width: 100px;
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
			
			.captcha-section {
				background: rgba(45, 55, 72, 0.8);
				border-color: #4a5568;
			}
			
			.captcha-display {
				background: linear-gradient(45deg, #4a5568, #2d3748);
				border-color: #4a5568;
				color: #f7fafc;
			}
			
			.captcha-header {
				color: #f7fafc;
			}
			
			.security-log {
				background: rgba(45, 55, 72, 0.8);
			}
			
			.security-log h6 {
				color: #f7fafc;
			}
			
			.log-entry {
				background: rgba(74, 85, 104, 0.6);
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

		<form method="POST" action="app/login.php" id="loginForm">
			<?php if (isset($_GET['error'])) {?>
				<div class="alert alert-danger" role="alert">
					<i class="fas fa-exclamation-triangle me-2"></i>
					Échec de l'authentification. Veuillez vérifier vos informations.
				</div>
			<?php } ?>

			<?php if (isset($_GET['success'])) {?>
				<div class="alert alert-success" role="alert">
					<i class="fas fa-check-circle me-2"></i>
					<?php echo stripcslashes($_GET['success']); ?>
				</div>
			<?php } ?>

			<!-- Dynamic Security Alert -->
			<div id="securityAlert" style="display: none;"></div>

			<div class="form-floating">
				<input type="text" class="form-control" name="user_name" id="username" placeholder="Username" required maxlength="50">
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

			<!-- CAPTCHA Container (Hidden by default) -->
			<div id="captchaContainer" class="captcha-section" style="display: none;">
				<div class="captcha-header">
					<i class="fas fa-shield-alt me-2"></i>
					<span>Vérification de sécurité requise</span>
				</div>
				<div class="captcha-box">
					<div class="captcha-display" id="captchaDisplay"></div>
					<button type="button" id="refreshCaptcha" class="captcha-refresh">
						<i class="fas fa-sync-alt"></i>
					</button>
				</div>
				<div class="form-floating">
					<input type="text" class="form-control" name="captcha" id="captchaInput" placeholder="Code de vérification" maxlength="6">
					<label for="captchaInput">
						<i class="fas fa-key me-2"></i>Code de vérification
					</label>
				</div>
			</div>

			<!-- Security Status -->
			<div id="securityStatus" class="security-status" style="display: none;">
				<div class="security-info">
					<i class="fas fa-exclamation-triangle"></i>
					<span id="securityMessage"></span>
				</div>
				<div class="security-timer" id="securityTimer"></div>
			</div>

			<button type="submit" class="btn btn-login" id="loginButton">
				<span id="loginButtonText">
					<i class="fas fa-sign-in-alt me-2"></i>
					Sign In
				</span>
				<div id="loginSpinner" style="display: none;">
					<i class="fas fa-spinner fa-spin me-2"></i>
					Vérification...
				</div>
			</button>

			<input type="hidden" name="security_token" id="securityToken">
			<input type="hidden" name="attempt_id" id="attemptId">
		</form>

		<!-- Security Log -->
		<div class="security-log" id="securityLog" style="display: none;">
			<h6><i class="fas fa-history me-2"></i>Journal de sécurité</h6>
			<div id="logEntries"></div>
		</div>

		<div class="form-footer">
			<p>&copy; 2024 Task Management System. All rights reserved.</p>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.2.0/crypto-js.min.js"></script>
	
	<script>
		// Configuration de sécurité
		const SECURITY_CONFIG = {
			maxAttempts: 3,
			lockoutDuration: 300000, // 5 minutes
			progressiveDelay: 2000, // 2 secondes base
			captchaThreshold: 2,
			sessionKey: 'login_security_' + window.location.hostname
		};

		// État de sécurité global
		let securityState = {
			attempts: 0,
			isLocked: false,
			lockoutEnd: null,
			lastAttempt: 0,
			captchaRequired: false,
			currentCaptcha: '',
			securityLog: []
		};

		// Éléments DOM
		const elements = {
			form: document.getElementById('loginForm'),
			loginButton: document.getElementById('loginButton'),
			loginButtonText: document.getElementById('loginButtonText'),
			loginSpinner: document.getElementById('loginSpinner'),
			username: document.getElementById('username'),
			password: document.getElementById('password'),
			captchaContainer: document.getElementById('captchaContainer'),
			captchaDisplay: document.getElementById('captchaDisplay'),
			captchaInput: document.getElementById('captchaInput'),
			refreshCaptcha: document.getElementById('refreshCaptcha'),
			securityStatus: document.getElementById('securityStatus'),
			securityMessage: document.getElementById('securityMessage'),
			securityTimer: document.getElementById('securityTimer'),
			securityAlert: document.getElementById('securityAlert'),
			securityLog: document.getElementById('securityLog'),
			logEntries: document.getElementById('logEntries'),
			securityToken: document.getElementById('securityToken'),
			attemptId: document.getElementById('attemptId')
		};

		// Initialisation
		document.addEventListener('DOMContentLoaded', function() {
			initializeSecurity();
			setupEventListeners();
			checkDarkMode();
		});

		// Initialisation du système de sécurité
		function initializeSecurity() {
			loadSecurityState();
			generateSecurityToken();
			checkLockoutStatus();
			updateSecurityDisplay();
			addSecurityLog('Système de sécurité initialisé', 'info');
		}

		// Génération du token de sécurité
		function generateSecurityToken() {
			const timestamp = Date.now();
			const random = Math.random().toString(36).substring(2);
			const token = CryptoJS.SHA256(timestamp + random + window.location.href).toString();
			elements.securityToken.value = token;
			elements.attemptId.value = 'attempt_' + timestamp + '_' + random;
		}

		// Chargement de l'état de sécurité depuis le stockage local
		function loadSecurityState() {
			try {
				const stored = localStorage.getItem(SECURITY_CONFIG.sessionKey);
				if (stored) {
					const parsed = JSON.parse(stored);
					securityState = { ...securityState, ...parsed };
				}
			} catch (e) {
				console.warn('Erreur lors du chargement de l\'état de sécurité');
			}
		}

		// Sauvegarde de l'état de sécurité
		function saveSecurityState() {
			try {
				localStorage.setItem(SECURITY_CONFIG.sessionKey, JSON.stringify(securityState));
			} catch (e) {
				console.warn('Erreur lors de la sauvegarde de l\'état de sécurité');
			}
		}

		// Vérification du statut de verrouillage
		function checkLockoutStatus() {
			const now = Date.now();
			
			if (securityState.isLocked && securityState.lockoutEnd) {
				if (now >= securityState.lockoutEnd) {
					// Déverrouillage automatique
					securityState.isLocked = false;
					securityState.lockoutEnd = null;
					securityState.attempts = 0;
					securityState.captchaRequired = false;
					addSecurityLog('Compte déverrouillé automatiquement', 'success');
					saveSecurityState();
				} else {
					// Toujours verrouillé
					showLockoutStatus();
					return;
				}
			}
			
			// Vérifier si CAPTCHA nécessaire
			if (securityState.attempts >= SECURITY_CONFIG.captchaThreshold) {
				securityState.captchaRequired = true;
				showCaptcha();
			}
		}

		// Génération du CAPTCHA
		function generateCaptcha() {
			const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
			let captcha = '';
			for (let i = 0; i < 6; i++) {
				captcha += chars.charAt(Math.floor(Math.random() * chars.length));
			}
			securityState.currentCaptcha = captcha;
			elements.captchaDisplay.textContent = captcha;
			elements.captchaInput.value = '';
			addSecurityLog('Nouveau CAPTCHA généré', 'info');
		}

		// Affichage du CAPTCHA
		function showCaptcha() {
			elements.captchaContainer.style.display = 'block';
			generateCaptcha();
			elements.captchaInput.focus();
		}

		// Masquage du CAPTCHA
		function hideCaptcha() {
			elements.captchaContainer.style.display = 'none';
			securityState.captchaRequired = false;
		}

		// Affichage du statut de verrouillage
		function showLockoutStatus() {
			const remaining = securityState.lockoutEnd - Date.now();
			const minutes = Math.ceil(remaining / 60000);
			
			elements.securityMessage.textContent = `Compte temporairement verrouillé pour ${minutes} minute(s)`;
			elements.securityStatus.style.display = 'block';
			elements.loginButton.disabled = true;
			
			updateLockoutTimer();
		}

		// Mise à jour du timer de verrouillage
		function updateLockoutTimer() {
			const interval = setInterval(() => {
				const now = Date.now();
				const remaining = securityState.lockoutEnd - now;
				
				if (remaining <= 0) {
					clearInterval(interval);
					checkLockoutStatus();
					updateSecurityDisplay();
					return;
				}
				
				const minutes = Math.floor(remaining / 60000);
				const seconds = Math.floor((remaining % 60000) / 1000);
				elements.securityTimer.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
			}, 1000);
		}

		// Affichage d'une alerte de sécurité
		function showSecurityAlert(message, type = 'warning') {
			const alertClasses = {
				danger: 'alert alert-danger',
				warning: 'alert alert-warning',
				success: 'alert alert-success',
				info: 'alert alert-info'
			};
			
			const icons = {
				danger: 'fas fa-exclamation-triangle',
				warning: 'fas fa-exclamation-circle',
				success: 'fas fa-check-circle',
				info: 'fas fa-info-circle'
			};
			
			elements.securityAlert.className = alertClasses[type];
			elements.securityAlert.innerHTML = `
				<i class="${icons[type]} me-2"></i>
				${message}
			`;
			elements.securityAlert.style.display = 'block';
			
			setTimeout(() => {
				elements.securityAlert.style.display = 'none';
			}, 5000);
		}

		// Ajout d'une entrée au journal de sécurité
		function addSecurityLog(message, type = 'info') {
			const timestamp = new Date().toLocaleTimeString();
			const entry = {
				time: timestamp,
				message: message,
				type: type
			};
			
			securityState.securityLog.unshift(entry);
			
			// Garder seulement les 20 dernières entrées
			if (securityState.securityLog.length > 20) {
				securityState.securityLog = securityState.securityLog.slice(0, 20);
			}
			
			updateSecurityLogDisplay();
			saveSecurityState();
		}

		// Mise à jour de l'affichage du journal
		function updateSecurityLogDisplay() {
			if (securityState.securityLog.length > 0) {
				elements.securityLog.style.display = 'block';
				elements.logEntries.innerHTML = securityState.securityLog
					.map(entry => `
						<div class="log-entry ${entry.type}">
							[${entry.time}] ${entry.message}
						</div>
					`).join('');
			}
		}

		// Mise à jour de l'affichage de sécurité
		function updateSecurityDisplay() {
			if (securityState.isLocked) {
				showLockoutStatus();
			} else {
				elements.securityStatus.style.display = 'none';
				elements.loginButton.disabled = false;
			}
			
			if (securityState.captchaRequired) {
				showCaptcha();
			}
		}

		// Validation avant soumission
		function validateSubmission() {
			const now = Date.now();
			
			// Vérifier le verrouillage
			if (securityState.isLocked) {
				showSecurityAlert('Compte temporairement verrouillé', 'danger');
				return false;
			}
			
			// Vérifier les délais entre tentatives
			const timeSinceLastAttempt = now - securityState.lastAttempt;
			const requiredDelay = SECURITY_CONFIG.progressiveDelay * Math.pow(2, securityState.attempts);
			
			if (timeSinceLastAttempt < requiredDelay && securityState.attempts > 0) {
				const remainingSeconds = Math.ceil((requiredDelay - timeSinceLastAttempt) / 1000);
				showSecurityAlert(`Veuillez patienter ${remainingSeconds} seconde(s) avant la prochaine tentative`, 'warning');
				return false;
			}
			
			// Vérifier le CAPTCHA si requis
			if (securityState.captchaRequired) {
				const captchaValue = elements.captchaInput.value.toUpperCase();
				if (!captchaValue || captchaValue !== securityState.currentCaptcha) {
					showSecurityAlert('Code de vérification incorrect', 'danger');
					generateCaptcha();
					return false;
				}
			}
			
			// Validation des champs
			if (!elements.username.value.trim() || !elements.password.value) {
				showSecurityAlert('Veuillez remplir tous les champs', 'warning');
				return false;
			}
			
			return true;
		}

		// Gestion de l'échec de connexion
		function handleLoginFailure() {
			securityState.attempts++;
			securityState.lastAttempt = Date.now();
			
			addSecurityLog(`Tentative de connexion échouée (${securityState.attempts}/${SECURITY_CONFIG.maxAttempts})`, 'warning');
			
			// Afficher CAPTCHA après le seuil
			if (securityState.attempts >= SECURITY_CONFIG.captchaThreshold) {
				securityState.captchaRequired = true;
				showCaptcha();
				showSecurityAlert('Vérification de sécurité requise après plusieurs échecs', 'warning');
			}
			
			// Verrouiller après le maximum de tentatives
			if (securityState.attempts >= SECURITY_CONFIG.maxAttempts) {
				securityState.isLocked = true;
				securityState.lockoutEnd = Date.now() + SECURITY_CONFIG.lockoutDuration;
				addSecurityLog('Compte verrouillé pour tentatives répétées', 'danger');
				showLockoutStatus();
			} else {
				const remaining = SECURITY_CONFIG.maxAttempts - securityState.attempts;
				showSecurityAlert(`Échec de l'authentification. ${remaining} tentative(s) restante(s)`, 'danger');
			}
			
			saveSecurityState();
		}

		// Configuration des écouteurs d'événements
		function setupEventListeners() {
			// Animation pour les champs de saisie
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

			// Refresh CAPTCHA
			elements.refreshCaptcha.addEventListener('click', generateCaptcha);

			// Soumission du formulaire
			elements.form.addEventListener('submit', function(e) {
				if (!validateSubmission()) {
					e.preventDefault();
					return;
				}
				
				// Afficher le spinner
				elements.loginButtonText.style.display = 'none';
				elements.loginSpinner.style.display = 'block';
				elements.loginButton.disabled = true;
				
				// Ajouter au journal
				addSecurityLog('Tentative de connexion en cours...', 'info');
				
				// Note: La gestion côté serveur déterminera le succès/échec
				// En cas d'échec, l'URL contiendra ?error=...
				setTimeout(() => {
					if (window.location.search.includes('error=')) {
						handleLoginFailure();
						// Réactiver le bouton
						elements.loginButtonText.style.display = 'block';
						elements.loginSpinner.style.display = 'none';
						elements.loginButton.disabled = false;
					}
				}, 1000);
			});

			// Auto-focus sur CAPTCHA
			elements.captchaInput.addEventListener('input', function() {
				if (this.value.length === 6) {
					// Auto-submit après saisie complète du CAPTCHA
					elements.form.dispatchEvent(new Event('submit'));
				}
			});
		}

		// Support du mode sombre
		function checkDarkMode() {
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
		}

		// Détection des erreurs d'URL pour déclencher la gestion d'échec
		if (window.location.search.includes('error=')) {
			setTimeout(() => {
				handleLoginFailure();
			}, 500);
		}

		// Nettoyage périodique des anciennes données de sécurité
		setInterval(() => {
			checkLockoutStatus();
		}, 60000); // Vérifier toutes les minutes
	</script>
</body>
</html>