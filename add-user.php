<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
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
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-content {
            flex: 1;
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

        .back-btn {
            background: rgba(74, 85, 104, 0.1);
            color: #4a5568;
            padding: 15px 25px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            cursor: pointer;
        }

        .back-btn:hover {
            background: rgba(74, 85, 104, 0.15);
            transform: translateY(-1px);
            text-decoration: none;
            color: #4a5568;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 600px;
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

        .error-alert {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            color: #c53030;
            padding: 20px 25px;
            margin-bottom: 25px;
            border: none;
            border-radius: 16px;
            border-left: 4px solid #e53e3e;
            font-weight: 500;
            animation: slideDown 0.5s ease-out;
            box-shadow: 0 4px 15px rgba(229, 62, 62, 0.2);
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

        .form-grid {
            display: grid;
            gap: 25px;
        }

        .input-group {
            position: relative;
        }

        .input-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .required-indicator {
            color: #e53e3e;
            font-size: 12px;
        }

        .input-field {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 400;
            color: #2d3748;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .input-field:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: rgba(255, 255, 255, 1);
        }

        .input-field::placeholder {
            color: #a0aec0;
        }

        .select-field {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
            padding-right: 50px;
            appearance: none;
            cursor: pointer;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            pointer-events: none;
        }

        .input-with-icon {
            padding-left: 50px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 16px;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: #667eea;
        }

        .form-tips {
            background: rgba(102, 126, 234, 0.05);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .form-tips h4 {
            color: #667eea;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .form-tips ul {
            margin: 0;
            padding-left: 20px;
            color: #4a5568;
            font-size: 14px;
        }

        .form-tips li {
            margin-bottom: 5px;
        }

        .role-preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .role-badge {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-admin {
            background: linear-gradient(135deg, #fed7a1, #fbb040);
            color: #c05621;
        }

        .role-employee {
            background: linear-gradient(135deg, #bee3f8, #90cdf4);
            color: #2c5282;
        }

        .password-strength {
            margin-top: 8px;
            font-size: 12px;
        }

        .strength-weak {
            color: #e53e3e;
        }

        .strength-medium {
            color: #dd6b20;
        }

        .strength-strong {
            color: #38a169;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .submit-btn {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 16px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
            min-width: 160px;
            justify-content: center;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .cancel-btn {
            background: rgba(74, 85, 104, 0.1);
            color: #4a5568;
            padding: 16px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            cursor: pointer;
            min-width: 160px;
            justify-content: center;
        }

        .cancel-btn:hover {
            background: rgba(74, 85, 104, 0.15);
            transform: translateY(-1px);
            text-decoration: none;
            color: #4a5568;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .section-1 {
                padding: 20px;
            }

            .page-header {
                padding: 20px;
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .page-title {
                font-size: 24px;
                justify-content: center;
            }

            .form-container {
                padding: 25px;
            }

            .form-actions {
                flex-direction: column;
            }

            .submit-btn,
            .cancel-btn {
                width: 100%;
            }

            .role-preview {
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

            .page-title {
                color: #f7fafc;
            }

            .page-subtitle {
                color: #cbd5e0;
            }

            .input-label {
                color: #e2e8f0;
            }

            .input-field {
                background: rgba(45, 55, 72, 0.8);
                border-color: #4a5568;
                color: #f7fafc;
            }

            .input-field:focus {
                background: rgba(45, 55, 72, 1);
                border-color: #667eea;
            }

            .input-field::placeholder {
                color: #718096;
            }

            .back-btn,
            .cancel-btn {
                background: rgba(255, 255, 255, 0.1);
                color: #e2e8f0;
                border-color: #4a5568;
            }

            .back-btn:hover,
            .cancel-btn:hover {
                background: rgba(255, 255, 255, 0.15);
                color: #e2e8f0;
            }

            .form-tips {
                background: rgba(102, 126, 234, 0.1);
                border-color: rgba(102, 126, 234, 0.3);
            }

            .form-tips h4 {
                color: #90cdf4;
            }

            .form-tips,
            .form-tips li {
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
                <div class="header-content">
                    <h1 class="page-title">
                        <i class="fas fa-user-plus"></i>
                        Add New User
                    </h1>
                    <p class="page-subtitle">Create a new user account and assign their role</p>
                </div>
                <a href="user.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Users
                </a>
            </div>

            <div class="form-container">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="error-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= stripcslashes($_GET['error']); ?>
                    </div>
                <?php } ?>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="success-alert">
                        <i class="fas fa-check-circle"></i>
                        <?= stripcslashes($_GET['success']); ?>
                    </div>
                <?php } ?>

                <div class="form-tips">
                    <h4>
                        <i class="fas fa-info-circle"></i>
                        User Account Guidelines
                    </h4>
                    <ul>
                        <li>Use a strong password with at least 8 characters</li>
                        <li>Choose a unique username that's easy to remember</li>
                        <li>Admin users have full access to all system features</li>
                        <li>Employee users can only manage their assigned tasks</li>
                    </ul>
                </div>

                <form method="POST" action="app/add-user.php" id="userForm">
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="full_name" class="input-label">
                                <i class="fas fa-user"></i>
                                Full Name
                                <span class="required-indicator">*</span>
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="text" 
                                    id="full_name"
                                    name="full_name" 
                                    class="input-field input-with-icon" 
                                    placeholder="Enter the user's full name"
                                    required 
                                    aria-required="true"
                                    maxlength="100"
                                >
                                <i class="fas fa-id-card input-icon"></i>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="user_name" class="input-label">
                                <i class="fas fa-at"></i>
                                Username
                                <span class="required-indicator">*</span>
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="text" 
                                    id="user_name"
                                    name="user_name" 
                                    class="input-field input-with-icon" 
                                    placeholder="Choose a unique username"
                                    required 
                                    aria-required="true"
                                    maxlength="50"
                                    pattern="[a-zA-Z0-9_]+"
                                    title="Username can only contain letters, numbers, and underscores"
                                >
                                <i class="fas fa-user-circle input-icon"></i>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="password" class="input-label">
                                <i class="fas fa-lock"></i>
                                Password
                                <span class="required-indicator">*</span>
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="password" 
                                    id="password"
                                    name="password" 
                                    class="input-field input-with-icon" 
                                    placeholder="Create a strong password"
                                    required 
                                    aria-required="true"
                                    minlength="6"
                                >
                                <i class="fas fa-key input-icon"></i>
                                <i class="fas fa-eye password-toggle" onclick="togglePassword()" id="passwordToggle"></i>
                            </div>
                            <div id="passwordStrength" class="password-strength"></div>
                        </div>

                        <div class="input-group">
                            <label for="role" class="input-label">
                                <i class="fas fa-user-shield"></i>
                                User Role
                                <span class="required-indicator">*</span>
                            </label>
                            <div style="position: relative;">
                                <select 
                                    id="role"
                                    name="role" 
                                    class="input-field select-field input-with-icon" 
                                    required 
                                    aria-required="true"
                                >
                                    <option value="">Select user role...</option>
                                    <option value="employee">Employee</option>
                                    <option value="admin">Administrator</option>
                                </select>
                                <i class="fas fa-users input-icon"></i>
                            </div>
                            <div class="role-preview">
                                <span class="role-badge role-employee">
                                    <i class="fas fa-user"></i>
                                    Employee
                                </span>
                                <span class="role-badge role-admin">
                                    <i class="fas fa-shield-alt"></i>
                                    Administrator
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn" id="submitBtn">
                            <i class="fas fa-user-plus"></i>
                            Create User
                        </button>
                        <a href="user.php" class="cancel-btn">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        // Highlight the active navigation item
        document.querySelector("#navList li:nth-child(2)").classList.add("active");

        // Password toggle functionality
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash password-toggle';
            } else {
                passwordField.type = 'password';
                toggleIcon.className = 'fas fa-eye password-toggle';
            }
        }

        // Password strength checker
        const passwordField = document.getElementById('password');
        const strengthDiv = document.getElementById('passwordStrength');

        passwordField.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let feedback = '';

            if (password.length >= 6) strength += 1;
            if (password.length >= 10) strength += 1;
            if (/[a-z]/.test(password)) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;

            if (password.length === 0) {
                feedback = '';
                strengthDiv.className = 'password-strength';
            } else if (strength <= 2) {
                feedback = '⚠️ Weak password';
                strengthDiv.className = 'password-strength strength-weak';
            } else if (strength <= 4) {
                feedback = '⚡ Medium strength';
                strengthDiv.className = 'password-strength strength-medium';
            } else {
                feedback = '✅ Strong password';
                strengthDiv.className = 'password-strength strength-strong';
            }

            strengthDiv.textContent = feedback;
        });

        // Form submission with loading state
        const form = document.getElementById('userForm');
        const submitBtn = document.getElementById('submitBtn');
        
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating User...';
        });

        // Form validation feedback
        const inputs = form.querySelectorAll('input[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.style.borderColor = '#e53e3e';
                } else {
                    this.style.borderColor = '#48bb78';
                }
            });

            input.addEventListener('input', function() {
                if (this.style.borderColor === 'rgb(229, 62, 62)' && this.value.trim() !== '') {
                    this.style.borderColor = '#e2e8f0';
                }
            });
        });

        // Username validation
        const usernameField = document.getElementById('user_name');
        usernameField.addEventListener('input', function() {
            const value = this.value;
            const validChars = /^[a-zA-Z0-9_]*$/;
            
            if (!validChars.test(value)) {
                this.value = value.replace(/[^a-zA-Z0-9_]/g, '');
            }
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

        // Smooth scroll to error/success messages
        if (document.querySelector('.error-alert, .success-alert')) {
            document.querySelector('.error-alert, .success-alert').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
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