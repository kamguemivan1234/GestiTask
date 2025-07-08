<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && $_SESSION['role'] == 'admin'&& isset($_POST['due_date'])) {
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
	$id = validate_input($_POST['id']);
	$due_date = validate_input($_POST['due_date']);

	if (empty($title)) {
		$em = "Title is required";
	    header("Location: ../edit-task.php?error=$em&id=$id");
	    exit();
	}else if (empty($description)) {
		$em = "Description is required";
	    header("Location: ../edit-task.php?error=$em&id=$id");
	    exit();
	}else if ($assigned_to == 0) {
		$em = "Select User";
	    header("Location: ../edit-task.php?error=$em&id=$id");
	    exit();
	}else {
    
       include "Model/Task.php";

       // Récupérer l'ancien fichier attaché
       $current_task = get_task_by_id($conn, $id);
       $attachment = $current_task['attachment'];

       // Gérer l'upload de nouveau fichier
       try {
           if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
               // Supprimer l'ancien fichier s'il existe
               if ($attachment) {
                   $old_file_path = '../uploads/' . $attachment;
                   if (file_exists($old_file_path)) {
                       unlink($old_file_path);
                   }
               }
               
               // Uploader le nouveau fichier
               $attachment = upload_file($_FILES['attachment']);
           }
       } catch (Exception $e) {
           $em = "File upload error: " . $e->getMessage();
           header("Location: ../edit-task.php?error=$em&id=$id");
           exit();
       }

       $data = array($title, $description, $assigned_to, $due_date, $attachment, $id);
       update_task($conn, $data);

       $em = "Task updated successfully";
	    header("Location: ../edit-task.php?success=$em&id=$id");
	    exit();
	}
}else {
   $em = "Unknown error occurred";
   header("Location: ../edit-task.php?error=$em");
   exit();
}

}else{ 
   $em = "First login";
   header("Location: ../login.php?error=$em");
   exit();
}
?>