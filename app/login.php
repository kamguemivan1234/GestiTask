<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();

// Vérifier si les champs sont envoyés
if (isset($_POST['user_name']) && isset($_POST['password'])) {

    // Inclure la connexion à la base de données
    include "../DB_connection.php";

    // Fonction de nettoyage des entrées
    function validate_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Nettoyer les données entrées par l'utilisateur
    $user_name = validate_input($_POST['user_name']);
    $password = validate_input($_POST['password']);

    // Vérifications
    if (empty($user_name)) {
        $em = "User name is required";
        header("Location: ../login.php?error=$em");
        exit();
    }

    if (empty($password)) {
        $em = "Password is required";
        header("Location: ../login.php?error=$em");
        exit();
    }

    try {
        // Préparer la requête SQL
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_name]);

        // Vérifier si l'utilisateur existe
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch();

            // Vérifier le mot de passe
            if (password_verify($password, $user['password'])) {

                // Stocker les infos en session
                $_SESSION['role'] = $user['role'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Rediriger selon le rôle
                header("Location: ../index.php");
                exit();
            } else {
                $em = "Incorrect username or password";
                header("Location: ../login.php?error=$em");
                exit();
            }
        } else {
            $em = "Incorrect username or password";
            header("Location: ../login.php?error=$em");
            exit();
        }

    } catch (PDOException $e) {
        // En cas d'erreur PDO
        $em = "Erreur serveur : " . $e->getMessage();
        header("Location: ../login.php?error=$em");
        exit();
    }

} else {
    $em = "Invalid request";
    header("Location: ../login.php?error=$em");
    exit();
}
