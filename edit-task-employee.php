<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .form-1 {
            max-width: 500px;
            padding: 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .input-holder {
            margin-bottom: 20px;
        }
        .input-holder label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        .input-holder p {
            margin: 0;
            font-size: 16px;
            color: #333;
        }
        .input-1 {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .edit-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 16px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
        .danger, .success {
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
        }
        .danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .title {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        .title a {
            float: right;
            font-size: 14px;
            background-color: #17a2b8;
            color: white;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        .title a:hover {
            background-color: #138496;
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Edit Task <a href="my_task.php">Back to Tasks</a></h4>

            <form class="form-1" method="POST" action="app/update-task-employee.php">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="danger"><?= stripcslashes($_GET['error']); ?></div>
                <?php } ?>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="success"><?= stripcslashes($_GET['success']); ?></div>
                <?php } ?>

                <div class="input-holder">
                    <label>Title</label>
                    <p><?= htmlspecialchars($task['title']) ?></p>
                </div>

                <div class="input-holder">
                    <label>Description</label>
                    <p><?= htmlspecialchars($task['description']) ?></p>
                </div>

                <div class="input-holder">
                    <label>Status</label>
                    <select name="status" class="input-1">
                        <option value="pending" <?= $task['status'] == "pending" ? "selected" : "" ?>>Pending</option>
                        <option value="in_progress" <?= $task['status'] == "in_progress" ? "selected" : "" ?>>In Progress</option>
                        <option value="completed" <?= $task['status'] == "completed" ? "selected" : "" ?>>Completed</option>
                    </select>
                </div>

                <input type="hidden" name="id" value="<?= htmlspecialchars($task['id']) ?>">

                <button type="submit" class="edit-btn">Update Task</button>
            </form>
        </section>
    </div>

    <script>
        document.querySelector("#navList li:nth-child(2)").classList.add("active");
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
