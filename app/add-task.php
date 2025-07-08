<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && $_SESSION['role'] == 'admin' && isset($_POST['due_date'])) {
	include "../DB_connection.php";

    function validate_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}

    function upload_file($file) {
        $upload_dir = '../uploads/';
        
        // Créer le dossier s'il n'existe pas
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Vérifications de sécurité
        $allowed_types = [
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
            'jpg', 'jpeg', 'png', 'gif', 'txt', 'zip', 'rar'
        ];
        
        $max_size = 10 * 1024 * 1024; // 10MB
        
        // Vérifier si un fichier a été uploadé
        if (!isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        
        // Vérifier la taille
        if ($file['size'] > $max_size) {
            throw new Exception("File size must be less than 10MB");
        }
        
        // Vérifier l'extension
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_extension, $allowed_types)) {
            throw new Exception("File type not allowed. Supported formats: " . implode(', ', $allowed_types));
        }
        
        // Créer un nom unique pour éviter les conflits
        $unique_name = date('Y-m-d_H-i-s') . '_' . uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $unique_name;
        
        // Déplacer le fichier
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            return $unique_name;
        } else {
            throw new Exception("Failed to upload file");
        }
    }

	$title = validate_input($_POST['title']);
	$description = validate_input($_POST['description']);
	$assigned_to = validate_input($_POST['assigned_to']);
	$due_date = validate_input($_POST['due_date']);
	$status = validate_input($_POST['status']);

	if (empty($title)) {
		$em = "Title is required";
	    header("Location: ../create_task.php?error=$em");
	    exit();
	}else if (empty($description)) {
		$em = "Description is required";
	    header("Location: ../create_task.php?error=$em");
	    exit();
	}else if ($assigned_to == 0) {
		$em = "Select User";
	    header("Location: ../create_task.php?error=$em");
	    exit();
	}else if (empty($status)) {
		$em = "Status is required";
	    header("Location: ../create_task.php?error=$em");
	    exit();
	}else {
    
       include "Model/Task.php";
       include "Model/Notification.php";

       // Gérer l'upload de fichier
       $attachment = null;
       try {
           if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
               $attachment = upload_file($_FILES['attachment']);
           }
       } catch (Exception $e) {
           $em = "File upload error: " . $e->getMessage();
           header("Location: ../create_task.php?error=$em");
           exit();
       }

       $data = array($title, $description, $assigned_to, $due_date, $status, $attachment);
       insert_task($conn, $data);

       $notif_data = array("'$title' has been assigned to you. Please review and start working on it", $assigned_to, 'New Task Assigned');
       insert_notification($conn, $notif_data);

       $em = "Task created successfully";
	    header("Location: ../create_task.php?success=$em");
	    exit();
	}
}else {
   $em = "Unknown error occurred";
   header("Location: ../create_task.php?error=$em");
   exit();
}

}else{ 
   $em = "First login";
   header("Location: ../create_task.php?error=$em");
   exit();
}
?>