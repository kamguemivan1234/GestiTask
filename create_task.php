<?php 
session_start();
if (isset($_SESSION['role'], $_SESSION['id']) && $_SESSION['role'] === "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Task</title>
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
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .page-subtitle {
            font-size: 16px;
            color: #718096;
            margin-top: 8px;
            font-weight: 400;
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
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

        .textarea-field {
            min-height: 120px;
            resize: vertical;
            font-family: 'Inter', sans-serif;
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

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #e2e8f0;
        }

        .submit-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
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
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            min-width: 160px;
            justify-content: center;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
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

        /* Status badges preview */
        .status-preview {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: linear-gradient(135deg, #fed7a1, #fbb040);
            color: #c05621;
        }

        .status-progress {
            background: linear-gradient(135deg, #bee3f8, #90cdf4);
            color: #2c5282;
        }

        .status-completed {
            background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
            color: #2f855a;
        }

        /* File upload styles */
        .file-upload-container {
            width: 100%;
        }

        .file-input {
            cursor: pointer;
            padding: 16px 50px 16px 20px;
        }

        .file-input::-webkit-file-upload-button {
            display: none;
        }

        .file-input::file-selector-button {
            display: none;
        }

        .file-info {
            margin-top: 12px;
            padding: 12px;
            background: rgba(102, 126, 234, 0.05);
            border: 1px solid rgba(102, 126, 234, 0.2);
            border-radius: 8px;
        }

        .file-preview {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .file-preview i {
            font-size: 18px;
            color: #667eea;
        }

        .remove-file {
            background: none;
            border: none;
            color: #e53e3e;
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            margin-left: auto;
            transition: background-color 0.3s ease;
        }

        .remove-file:hover {
            background: rgba(229, 62, 62, 0.1);
        }

        .file-help {
            margin-top: 8px;
            color: #718096;
            font-size: 12px;
            line-height: 1.4;
        }

        .file-help i {
            color: #667eea;
            margin-right: 4px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .section-1 {
                padding: 20px;
            }

            .page-header,
            .form-container {
                padding: 25px;
            }

            .page-title {
                font-size: 24px;
                flex-direction: column;
                gap: 10px;
            }

            .form-actions {
                flex-direction: column;
            }

            .submit-btn,
            .cancel-btn {
                width: 100%;
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

            .cancel-btn {
                background: rgba(255, 255, 255, 0.1);
                color: #e2e8f0;
                border-color: #4a5568;
            }

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

            .file-info {
                background: rgba(102, 126, 234, 0.1);
                border-color: rgba(102, 126, 234, 0.3);
            }

            .file-help {
                color: #cbd5e0;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php"; ?>

    <div class="body">
        <?php include "inc/nav.php"; ?>

        <section class="section-1">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-plus-circle"></i>
                    Create New Task
                </h1>
                <p class="page-subtitle">Assign tasks to team members and track progress</p>
            </div>

            <div class="form-container">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="error-alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= htmlspecialchars(stripslashes($_GET['error'])); ?>
                    </div>
                <?php } ?>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="success-alert">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars(stripslashes($_GET['success'])); ?>
                    </div>
                <?php } ?>

                <div class="form-tips">
                    <h4>
                        <i class="fas fa-lightbulb"></i>
                        Tips for Creating Effective Tasks
                    </h4>
                    <ul>
                        <li>Write clear and specific task titles</li>
                        <li>Provide detailed descriptions with expected outcomes</li>
                        <li>Set realistic due dates to ensure quality work</li>
                        <li>Choose the right team member based on their skills</li>
                        <li>Attach relevant files to provide context and resources</li>
                    </ul>
                </div>

                <form method="POST" action="app/add-task.php" id="taskForm" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="input-group">
                            <label for="title" class="input-label">
                                <i class="fas fa-heading"></i>
                                Task Title
                                <span class="required-indicator">*</span>
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="text" 
                                    id="title" 
                                    name="title" 
                                    class="input-field input-with-icon" 
                                    placeholder="Enter a clear and descriptive task title"
                                    required 
                                    aria-required="true"
                                    maxlength="255"
                                >
                                <i class="fas fa-tasks input-icon"></i>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="description" class="input-label">
                                <i class="fas fa-align-left"></i>
                                Task Description
                                <span class="required-indicator">*</span>
                            </label>
                            <textarea 
                                id="description" 
                                name="description" 
                                class="input-field textarea-field" 
                                placeholder="Provide detailed information about what needs to be accomplished, expected deliverables, and any specific requirements..."
                                required 
                                aria-required="true"
                            ></textarea>
                        </div>

                        <div class="input-group">
                            <label for="due_date" class="input-label">
                                <i class="fas fa-calendar-alt"></i>
                                Due Date
                            </label>
                            <div style="position: relative;">
                                <input 
                                    type="date" 
                                    id="due_date" 
                                    name="due_date" 
                                    class="input-field input-with-icon"
                                    min="<?= date('Y-m-d') ?>"
                                >
                                <i class="fas fa-calendar input-icon"></i>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="assigned_to" class="input-label">
                                <i class="fas fa-user-check"></i>
                                Assign To
                                <span class="required-indicator">*</span>
                            </label>
                            <div style="position: relative;">
                                <select 
                                    id="assigned_to" 
                                    name="assigned_to" 
                                    class="input-field select-field input-with-icon" 
                                    required 
                                    aria-required="true"
                                >
                                    <option value="">Choose a team member...</option>
                                    <?php if (!empty($users)) {
                                        foreach ($users as $user) { ?>
                                            <option value="<?= $user['id']; ?>">
                                                <?= htmlspecialchars($user['full_name']); ?> 
                                                (<?= ucfirst($user['role']) ?>)
                                            </option>
                                    <?php } } ?>
                                </select>
                                <i class="fas fa-user input-icon"></i>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="status" class="input-label">
                                <i class="fas fa-flag"></i>
                                Initial Status
                                <span class="required-indicator">*</span>
                            </label>
                            <div style="position: relative;">
                                <select 
                                    id="status" 
                                    name="status" 
                                    class="input-field select-field input-with-icon" 
                                    required 
                                    aria-required="true"
                                >
                                    <option value="">Select initial status...</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                                <i class="fas fa-flag input-icon"></i>
                            </div>
                            <div class="status-preview">
                                <span class="status-badge status-pending">Pending</span>
                                <span class="status-badge status-progress">In Progress</span>
                                <span class="status-badge status-completed">Completed</span>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="attachment" class="input-label">
                                <i class="fas fa-paperclip"></i>
                                Task Attachment
                                <span style="font-size: 12px; color: #718096; font-weight: 400;">(Optional)</span>
                            </label>
                            <div class="file-upload-container">
                                <div style="position: relative;">
                                    <input 
                                        type="file" 
                                        id="attachment" 
                                        name="attachment" 
                                        class="input-field file-input input-with-icon" 
                                        accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.txt,.zip,.rar"
                                        onchange="updateFileInfo(this)"
                                    >
                                    <i class="fas fa-cloud-upload-alt input-icon"></i>
                                </div>
                                <div class="file-info" id="fileInfo" style="display: none;">
                                    <div class="file-preview">
                                        <i class="fas fa-file" id="fileIcon"></i>
                                        <span id="fileName">No file selected</span>
                                        <span id="fileSize"></span>
                                        <button type="button" class="remove-file" onclick="removeFile()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="file-help">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        Supported formats: PDF, Word, Excel, PowerPoint, Images (JPG, PNG, GIF), Text, ZIP
                                        <br>
                                        Maximum file size: 10MB
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="submit-btn" id="submitBtn">
                            <i class="fas fa-plus"></i>
                            Create Task
                        </button>
                        <a href="tasks.php" class="cancel-btn">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <script>
        // Highlight the correct navigation item
        document.querySelector("#navList li:nth-child(3)").classList.add("active");

        // Form validation and enhancements
        const form = document.getElementById('taskForm');
        const submitBtn = document.getElementById('submitBtn');
        
        // File handling functions
        function updateFileInfo(input) {
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');
            const fileSize = document.getElementById('fileSize');
            const fileIcon = document.getElementById('fileIcon');
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const maxSize = 10 * 1024 * 1024; // 10MB
                
                if (file.size > maxSize) {
                    showCustomAlert('File size must be less than 10MB');
                    input.value = '';
                    return;
                }
                
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                
                // Set appropriate icon based on file type
                const extension = file.name.split('.').pop().toLowerCase();
                fileIcon.className = getFileIcon(extension);
                
                fileInfo.style.display = 'block';
            } else {
                fileInfo.style.display = 'none';
            }
        }
        
        function removeFile() {
            const input = document.getElementById('attachment');
            const fileInfo = document.getElementById('fileInfo');
            
            input.value = '';
            fileInfo.style.display = 'none';
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        function getFileIcon(extension) {
            const icons = {
                'pdf': 'fas fa-file-pdf',
                'doc': 'fas fa-file-word',
                'docx': 'fas fa-file-word',
                'xls': 'fas fa-file-excel',
                'xlsx': 'fas fa-file-excel',
                'ppt': 'fas fa-file-powerpoint',
                'pptx': 'fas fa-file-powerpoint',
                'jpg': 'fas fa-file-image',
                'jpeg': 'fas fa-file-image',
                'png': 'fas fa-file-image',
                'gif': 'fas fa-file-image',
                'txt': 'fas fa-file-alt',
                'zip': 'fas fa-file-archive',
                'rar': 'fas fa-file-archive'
            };
            
            return icons[extension] || 'fas fa-file';
        }
        
        function showCustomAlert(message) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.right = '0';
            modal.style.bottom = '0';
            modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.style.zIndex = '1000';
            modal.innerHTML = `
                <div style="background: white; padding: 24px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); max-width: 400px; width: 90%;">
                    <p style="margin: 0 0 16px 0; color: #374151;">${message}</p>
                    <div style="text-align: right;">
                        <button onclick="this.closest('div').remove()" style="padding: 8px 16px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer;">OK</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }
        
        // Form submission with loading state
        form.addEventListener('submit', function(e) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Task...';
        });

        // Auto-resize textarea
        const textarea = document.getElementById('description');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });

        // Form validation feedback
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
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
    $em = urlencode("First login");
    header("Location: login.php?error=$em");
    exit();
}
?>