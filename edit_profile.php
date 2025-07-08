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
    <title>Edit Profile</title>
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

        .back-btn {
            background: linear-gradient(135deg, #718096, #4a5568);
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
            box-shadow: 0 4px 15px rgba(113, 128, 150, 0.3);
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(113, 128, 150, 0.4);
            color: white;
            text-decoration: none;
        }

        .profile-container {
            max-width: 900px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
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

        .form-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
            border-radius: 24px 24px 0 0;
        }

        .section-title {
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-description {
            color: #718096;
            font-size: 14px;
            margin-bottom: 30px;
            line-height: 1.5;
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
            grid-column: 1 / -1;
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

        .form-group {
            margin-bottom: 25px;
            position: relative;
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
            padding: 16px 20px 16px 55px;
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

        .password-strength {
            margin-top: 10px;
            display: none;
        }

        .strength-bar {
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 4px;
        }

        .strength-text {
            font-size: 12px;
            font-weight: 500;
        }

        .strength-weak .strength-fill {
            width: 33%;
            background: #ef4444;
        }
        .strength-weak .strength-text {
            color: #ef4444;
        }

        .strength-medium .strength-fill {
            width: 66%;
            background: #f59e0b;
        }
        .strength-medium .strength-text {
            color: #f59e0b;
        }

        .strength-strong .strength-fill {
            width: 100%;
            background: #10b981;
        }
        .strength-strong .strength-text {
            color: #10b981;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e2e8f0;
            grid-column: 1 / -1;
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

        .user-avatar {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            font-weight: 600;
            margin: 0 auto 20px;
            box-shadow: 0 15px 35px rgba(102, 126, 234, 0.3);
        }

        .current-info {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .current-info-label {
            font-size: 12px;
            color: #667eea;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }

        .current-info-value {
            font-size: 16px;
            color: #4a5568;
            font-weight: 600;
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

            .profile-container {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            .form-section {
                padding: 30px 25px;
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
            .form-section {
                background: rgba(45, 55, 72, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .page-title,
            .section-title,
            .form-label,
            .current-info-value {
                color: #f7fafc;
            }

            .section-description,
            .form-help {
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

            .current-info {
                background: rgba(74, 85, 104, 0.6);
                border-color: #4a5568;
            }

            .current-info-label {
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
                    <i class="fas fa-user-edit"></i>
                    Edit Profile
                </h1>
                <a href="profile.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Profile
                </a>
            </div>

            <form method="POST" action="app/update-profile.php" id="profileForm">
                <div class="profile-container">
                    <?php if (isset($_GET['error'])) { ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= stripcslashes($_GET['error']); ?>
                        </div>
                    <?php } ?>

                    <?php if (isset($_GET['success'])) { ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= stripcslashes($_GET['success']); ?>
                        </div>
                    <?php } ?>

                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <div class="user-avatar">
                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                        </div>
                        
                        <h3 class="section-title">
                            <i class="fas fa-user"></i>
                            Personal Information
                        </h3>
                        <p class="section-description">
                            Update your personal details and account information.
                        </p>

                        <div class="current-info">
                            <div class="current-info-label">Current Username</div>
                            <div class="current-info-value">@<?= htmlspecialchars($user['username']) ?></div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Full Name <span class="required">*</span>
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="text" 
                                    name="full_name" 
                                    class="form-input" 
                                    placeholder="Enter your full name" 
                                    value="<?= htmlspecialchars($user['full_name']) ?>"
                                    required
                                >
                                <i class="input-icon fas fa-user"></i>
                            </div>
                            <div class="form-help">This will be displayed as your display name</div>
                        </div>
                    </div>

                    <!-- Security Section -->
                    <div class="form-section">
                        <h3 class="section-title">
                            <i class="fas fa-shield-alt"></i>
                            Security Settings
                        </h3>
                        <p class="section-description">
                            Change your password to keep your account secure.
                        </p>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i>
                                Current Password <span class="required">*</span>
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="password" 
                                    name="password" 
                                    class="form-input" 
                                    placeholder="Enter your current password"
                                    required
                                >
                                <i class="input-icon fas fa-lock"></i>
                            </div>
                            <div class="form-help">Enter your current password to verify changes</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-key"></i>
                                New Password
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="password" 
                                    name="new_password" 
                                    class="form-input" 
                                    placeholder="Enter new password"
                                    id="newPassword"
                                >
                                <i class="input-icon fas fa-key"></i>
                            </div>
                            <div class="password-strength" id="passwordStrength">
                                <div class="strength-bar">
                                    <div class="strength-fill"></div>
                                </div>
                                <div class="strength-text">Password strength: <span id="strengthText">-</span></div>
                            </div>
                            <div class="form-help">Leave blank to keep current password</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-check-double"></i>
                                Confirm New Password
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="password" 
                                    name="confirm_password" 
                                    class="form-input" 
                                    placeholder="Confirm new password"
                                    id="confirmPassword"
                                >
                                <i class="input-icon fas fa-check-double"></i>
                            </div>
                            <div class="form-help">Re-enter your new password to confirm</div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="profile.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i>
                            Update Profile
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>

    <script>
        // Highlight active navigation
        document.querySelector("#navList li:nth-child(3)").classList.add("active");

        // Form validation and enhancement
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('profileForm');
            const inputs = form.querySelectorAll('.form-input');
            const submitBtn = document.getElementById('submitBtn');
            const newPasswordInput = document.getElementById('newPassword');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const passwordStrength = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');

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

            // Password strength checker
            newPasswordInput.addEventListener('input', function() {
                const password = this.value;
                if (password.length > 0) {
                    passwordStrength.style.display = 'block';
                    checkPasswordStrength(password);
                } else {
                    passwordStrength.style.display = 'none';
                }
            });

            // Confirm password validation
            confirmPasswordInput.addEventListener('input', function() {
                const newPassword = newPasswordInput.value;
                const confirmPassword = this.value;
                
                if (confirmPassword.length > 0) {
                    if (newPassword === confirmPassword) {
                        this.classList.add('valid');
                        this.classList.remove('invalid');
                    } else {
                        this.classList.add('invalid');
                        this.classList.remove('valid');
                    }
                }
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

            function checkPasswordStrength(password) {
                let strength = 0;
                const strengthElement = passwordStrength;
                
                // Length check
                if (password.length >= 8) strength++;
                // Uppercase check
                if (/[A-Z]/.test(password)) strength++;
                // Lowercase check
                if (/[a-z]/.test(password)) strength++;
                // Number check
                if (/\d/.test(password)) strength++;
                // Special character check
                if (/[^A-Za-z0-9]/.test(password)) strength++;

                // Remove all strength classes
                strengthElement.classList.remove('strength-weak', 'strength-medium', 'strength-strong');
                
                if (strength <= 2) {
                    strengthElement.classList.add('strength-weak');
                    strengthText.textContent = 'Weak';
                } else if (strength <= 4) {
                    strengthElement.classList.add('strength-medium');
                    strengthText.textContent = 'Medium';
                } else {
                    strengthElement.classList.add('strength-strong');
                    strengthText.textContent = 'Strong';
                }
            }

            // Form submission with loading state
            form.addEventListener('submit', function(e) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                submitBtn.disabled = true;
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