<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
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

        .add-user-btn {
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

        .add-user-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(72, 187, 120, 0.4);
            color: white;
            text-decoration: none;
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
        }

        .stat-total {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .stat-admins {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
        }

        .stat-employees {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: white;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: #718096;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .users-container {
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

        .user-card {
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

        .user-card::before {
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

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .user-card:hover::before {
            opacity: 1;
        }

        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .user-number {
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

        .user-info {
            flex: 1;
            margin-left: 20px;
        }

        .user-name {
            font-size: 20px;
            font-weight: 600;
            color: #2d3748;
            margin: 0 0 5px 0;
            line-height: 1.3;
        }

        .user-username {
            color: #718096;
            font-size: 15px;
            font-weight: 500;
        }

        .role-badge {
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

        .role-admin {
            background: linear-gradient(135deg, #fed7a1, #fbb040);
            color: #c05621;
            border: 1px solid #f6ad55;
        }

        .role-employee {
            background: linear-gradient(135deg, #bee3f8, #90cdf4);
            color: #2c5282;
            border: 1px solid #63b3ed;
        }

        .user-actions {
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
                flex-direction: column;
                align-items: stretch;
                text-align: center;
            }

            .page-title {
                font-size: 24px;
                justify-content: center;
            }

            .user-card {
                padding: 20px;
            }

            .user-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .user-info {
                margin-left: 0;
            }

            .user-actions {
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .section-1 {
                background: linear-gradient(135deg, #1a202c 0%, #2d3748 100%);
            }

            .page-header,
            .user-card,
            .empty-state,
            .stat-card {
                background: rgba(45, 55, 72, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .page-title,
            .user-name,
            .stat-number {
                color: #f7fafc;
            }

            .page-subtitle,
            .user-username,
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
                <div class="header-content">
                    <h1 class="page-title">
                        <i class="fas fa-users"></i>
                        Manage Users
                    </h1>
                    <p class="page-subtitle">Add, edit, and manage system users</p>
                </div>
                <a href="add-user.php" class="add-user-btn">
                    <i class="fas fa-plus"></i>
                    Add New User
                </a>
            </div>

            <?php if (isset($_GET['success'])) { ?>
                <div class="success-alert">
                    <i class="fas fa-check-circle"></i>
                    <?= stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>

            <?php if ($users != 0) { ?>
                <?php 
                $total_users = count($users);
                $admin_count = 0;
                $employee_count = 0;
                
                foreach ($users as $user) {
                    if ($user['role'] == 'admin') {
                        $admin_count++;
                    } else {
                        $employee_count++;
                    }
                }
                ?>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon stat-total">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-number"><?= $total_users ?></div>
                        <div class="stat-label">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-admins">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-number"><?= $admin_count ?></div>
                        <div class="stat-label">Administrators</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon stat-employees">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-number"><?= $employee_count ?></div>
                        <div class="stat-label">Employees</div>
                    </div>
                </div>

                <div class="users-container">
                    <?php $i = 0; foreach ($users as $user) { ?>
                    <div class="user-card">
                        <div class="user-header">
                            <div class="user-number"><?= ++$i ?></div>
                            <div class="user-info">
                                <h3 class="user-name"><?= htmlspecialchars($user['full_name']) ?></h3>
                                <div class="user-username">@<?= htmlspecialchars($user['username']) ?></div>
                            </div>
                            <div class="role-badge <?= $user['role'] == 'admin' ? 'role-admin' : 'role-employee' ?>">
                                <i class="fas fa-<?= $user['role'] == 'admin' ? 'shield-alt' : 'user' ?>"></i>
                                <?= htmlspecialchars(ucfirst($user['role'])) ?>
                            </div>
                        </div>

                        <div class="user-actions">
                            <a href="edit-user.php?id=<?= $user['id'] ?>" class="edit-btn">
                                <i class="fas fa-edit"></i>
                                Edit
                            </a>
                            <a href="delete-user.php?id=<?= $user['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this user?');">
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
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>No Users Found</h3>
                    <p>No users have been created yet. Start by adding your first user to the system.</p>
                </div>
            <?php } ?>
        </section>
    </div>

    <script>
        // Highlight the "Manage Users" tab in sidebar
        document.querySelector("#navList li:nth-child(2)").classList.add("active");

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