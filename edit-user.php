<?php 
session_start();
if (isset($_SESSION['role'], $_SESSION['id']) && $_SESSION['role'] === "admin") {
    require_once "DB_connection.php";
    require_once "app/Model/User.php";

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        header("Location: user.php");
        exit();
    }

    $id = (int) $_GET['id'];
    $user = get_user_by_id($conn, $id);

    if (!$user) {
        header("Location: user.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Edit User</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="css/style.css">
	<style>
		.form-container {
			max-width: 500px;
			margin: auto;
			background: #fff;
			padding: 30px;
			border-radius: 10px;
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
		}
		label {
			display: block;
			margin-bottom: 5px;
			font-weight: 500;
		}
		.input-1 {
			width: 100%;
			padding: 10px;
			margin-bottom: 20px;
			border-radius: 5px;
			border: 1px solid #ccc;
		}
		.edit-btn {
			background-color: #007bff;
			color: #fff;
			padding: 10px 20px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-weight: bold;
		}
		.edit-btn:hover {
			background-color: #0056b3;
		}
	</style>
</head>
<body>
	<input type="checkbox" id="checkbox">
	<?php include "inc/header.php"; ?>
	<div class="body">
		<?php include "inc/nav.php"; ?>
		
		<section class="section-1">
			<h4 class="title">Edit User <a href="user.php" style="float:right;">&larr; Back</a></h4>

			<div class="form-container">
				<form method="POST" action="app/update-user.php">
					<?php if (isset($_GET['error'])): ?>
						<div class="danger" role="alert">
							<?= htmlspecialchars(stripcslashes($_GET['error'])) ?>
						</div>
					<?php endif; ?>

					<?php if (isset($_GET['success'])): ?>
						<div class="success" role="alert">
							<?= htmlspecialchars(stripcslashes($_GET['success'])) ?>
						</div>
					<?php endif; ?>

					<input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">

					<div class="input-holder">
						<label for="full_name">Full Name</label>
						<input type="text" id="full_name" name="full_name" class="input-1" required
							value="<?= htmlspecialchars($user['full_name']) ?>">
					</div>

					<div class="input-holder">
						<label for="user_name">Username</label>
						<input type="text" id="user_name" name="user_name" class="input-1" required
							value="<?= htmlspecialchars($user['username']) ?>">
					</div>

					<div class="input-holder">
						<label for="password">Password</label>
						<input type="password" id="password" name="password" class="input-1" placeholder="Leave blank to keep current password">
					</div>

					<button type="submit" class="edit-btn">Update User</button>
				</form>
			</div>
		</section>
	</div>

	<script>
		document.querySelector("#navList li:nth-child(2)").classList.add("active");
	</script>
</body>
</html>
<?php 
} else {
	header("Location: login.php?error=Access denied. Please login as admin.");
	exit();
}
?>
