<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";

    $text = "All Tasks";
    $filter_type = "";

    if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") {
        $text = "Due Today";
        $filter_type = "due_today";
        $tasks = get_all_tasks_due_today($conn);
        $num_task = count_tasks_due_today($conn);
    } elseif (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") {
        $text = "Overdue Tasks";
        $filter_type = "overdue";
        $tasks = get_all_tasks_overdue($conn);
        $num_task = count_tasks_overdue($conn);
    } elseif (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") {
        $text = "No Deadline";
        $filter_type = "no_deadline";
        $tasks = get_all_tasks_NoDeadline($conn);
        $num_task = count_tasks_NoDeadline($conn);
    } else {
        $tasks = get_all_tasks($conn);
        $num_task = count_tasks($conn);
        $filter_type = "all";
    }

    $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Tasks</title>
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

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
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

        .create-task-btn {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 15px 25px;
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
        }

        .create-task-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4);
            color: white;
            text-decoration: none;
        }

        .filter-tabs {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .filter-tab:not(.active) {
            background: rgba(255, 255, 255, 0.7);
            color: #4a5568;
            border-color: #e2e8f0;
        }

        .filter-tab:not(.active):hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border-color: #667eea;
            text-decoration: none;
        }

        .task-counter {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 25px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .counter-number {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .counter-label {
            font-size: 14px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
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
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .task-meta {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
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

        .assigned-icon {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .due-date-icon {
            background: rgba(255, 107, 107, 0.1);
            color: #ff6b6b;
        }

        .status-icon {
            background: rgba(72, 187, 120, 0.1);
            color: #48bb78;
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

        .task-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .edit-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
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

        .delete-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
            color: white;
            text-decoration: none;
        }

        .due-date-warning {
            color: #e53e3e !important;
            font-weight: 700;
        }

        .due-date-today {
            color: #dd6b20 !important;
            font-weight: 700;
        }

        .no-deadline {
            color: #718096 !important;
            font-style: italic;
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .section-1 {
                padding: 20px;
            }

            .page-header {
                padding: 20px;
            }

            .header-top {
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .page-title {
                font-size: 24px;
                justify-content: center;
            }

            .filter-tabs {
                justify-content: center;
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
                text-align: center;
            }

            .task-title {
                margin-left: 0;
            }

            .task-actions {
                justify-content: center;
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
            .task-counter {
                background: rgba(45, 55, 72, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .page-title,
            .task-title {
                color: #f7fafc;
            }

            .page-subtitle,
            .task-description,
            .meta-value {
                color: #cbd5e0;
            }

            .meta-label {
                color: #a0aec0;
            }

            .filter-tab:not(.active) {
                background: rgba(255, 255, 255, 0.1);
                color: #e2e8f0;
                border-color: #4a5568;
            }

            .filter-tab:not(.active):hover {
                background: rgba(102, 126, 234, 0.2);
                color: #90cdf4;
                border-color: #90cdf4;
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
                <div class="header-top">
                    <div class="header-content">
                        <h1 class="page-title">
                            <i class="fas fa-tasks"></i>
                            All Tasks
                        </h1>
                        <p class="page-subtitle">Manage and monitor all project tasks</p>
                    </div>
                    <a href="create_task.php" class="create-task-btn">
                        <i class="fas fa-plus"></i>
                        Create Task
                    </a>
                </div>

                <div class="filter-tabs">
                    <a href="tasks.php" class="filter-tab <?= $filter_type == 'all' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i>
                        All Tasks
                    </a>
                    <a href="tasks.php?due_date=Due Today" class="filter-tab <?= $filter_type == 'due_today' ? 'active' : '' ?>">
                        <i class="fas fa-clock"></i>
                        Due Today
                    </a>
                    <a href="tasks.php?due_date=Overdue" class="filter-tab <?= $filter_type == 'overdue' ? 'active' : '' ?>">
                        <i class="fas fa-exclamation-triangle"></i>
                        Overdue
                    </a>
                    <a href="tasks.php?due_date=No Deadline" class="filter-tab <?= $filter_type == 'no_deadline' ? 'active' : '' ?>">
                        <i class="fas fa-calendar-times"></i>
                        No Deadline
                    </a>
                </div>
            </div>

            <div class="task-counter">
                <div class="counter-number"><?= $num_task ?></div>
                <div class="counter-label"><?= htmlspecialchars($text) ?></div>
            </div>

            <?php if (isset($_GET['success'])) { ?>
                <div class="success-alert">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars(stripcslashes($_GET['success'])) ?>
                </div>
            <?php } ?>

            <?php if ($tasks != 0) { ?>
                <div class="tasks-container">
                    <?php $i = 0; foreach ($tasks as $task) { 
                        // Determine due date styling
                        $due_date_class = '';
                        $due_date_text = '';
                        if ($task['due_date'] === "" || $task['due_date'] === null) {
                            $due_date_text = "No Deadline";
                            $due_date_class = 'no-deadline';
                        } else {
                            $due_date = new DateTime($task['due_date']);
                            $today = new DateTime();
                            $due_date_text = $due_date->format('M d, Y');
                            
                            if ($due_date < $today) {
                                $due_date_class = 'due-date-warning';
                            } elseif ($due_date->format('Y-m-d') === $today->format('Y-m-d')) {
                                $due_date_class = 'due-date-today';
                            }
                        }

                        // Find assigned user
                        $assigned_user = "Unassigned";
                        foreach ($users as $user) {
                            if ($user['id'] == $task['assigned_to']) {
                                $assigned_user = htmlspecialchars($user['full_name']);
                                break;
                            }
                        }

                        // Determine status class
                        $status_class = 'status-pending';
                        if (strtolower($task['status']) === 'completed') {
                            $status_class = 'status-completed';
                        } elseif (strtolower($task['status']) === 'in progress') {
                            $status_class = 'status-progress';
                        }
                    ?>
                    <div class="task-card">
                        <div class="task-header">
                            <div class="task-number"><?= ++$i ?></div>
                            <h3 class="task-title"><?= htmlspecialchars($task['title']) ?></h3>
                        </div>
                        
                        <div class="task-description">
                            <?= htmlspecialchars($task['description']) ?>
                        </div>

                        <div class="task-meta">
                            <div class="meta-item">
                                <div class="meta-icon assigned-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="meta-text">
                                    <div class="meta-label">Assigned To</div>
                                    <div class="meta-value"><?= $assigned_user ?></div>
                                </div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-icon due-date-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="meta-text">
                                    <div class="meta-label">Due Date</div>
                                    <div class="meta-value <?= $due_date_class ?>"><?= $due_date_text ?></div>
                                </div>
                            </div>
                            <div class="meta-item">
                                <div class="meta-icon status-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="meta-text">
                                    <div class="meta-label">Status</div>
                                    <div class="meta-value">
                                        <span class="status-badge <?= $status_class ?>">
                                            <?php if ($status_class === 'status-completed') { ?>
                                                <i class="fas fa-check"></i>
                                            <?php } elseif ($status_class === 'status-progress') { ?>
                                                <i class="fas fa-clock"></i>
                                            <?php } else { ?>
                                                <i class="fas fa-pause"></i>
                                            <?php } ?>
                                            <?= htmlspecialchars($task['status']) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="task-actions">
                            <a href="edit-task.php?id=<?= urlencode($task['id']) ?>" class="edit-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <a href="delete-task.php?id=<?= urlencode($task['id']) ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this task?');">
                                <i class="fas fa-trash"></i>
                                Delete
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
                    <h3>No Tasks Found</h3>
                    <p>No tasks match the current filter. Try adjusting your filters or create a new task.</p>
                </div>
            <?php } ?>
        </section>
    </div>

    <script>
        // Highlight the active navigation item
        var active = document.querySelector("#navList li:nth-child(4)");
        if (active) active.classList.add("active");

        // Add loading animation to buttons
        document.querySelectorAll('.edit-btn, .delete-btn').forEach(btn => {
            if (!btn.onclick) { // Only add to edit buttons (delete has onclick already)
                btn.addEventListener('click', function() {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                });
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

        // Smooth scroll to success messages
        if (document.querySelector('.success-alert')) {
            document.querySelector('.success-alert').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    </script>
</body>
</html>
<?php 
} else {
    $em = "First login";
    header("Location: login.php?error=$em");
    exit();
}
?>