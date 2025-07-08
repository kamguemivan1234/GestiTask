<?php
session_start();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Vérifier que le paramètre file est fourni
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("File not specified");
}

$filename = basename($_GET['file']);
$filepath = 'uploads/' . $filename;

// Vérifier que le fichier existe
if (!file_exists($filepath)) {
    die("File not found");
}

// Vérifier que le fichier est dans le dossier uploads (sécurité)
$realpath = realpath($filepath);
$upload_dir = realpath('uploads/');

if (strpos($realpath, $upload_dir) !== 0) {
    die("Access denied");
}

// Déterminer le type MIME
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime_type = finfo_file($finfo, $filepath);
finfo_close($finfo);

// Envoyer les headers pour le téléchargement
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . filesize($filepath));
header('Cache-Control: must-revalidate');
header('Pragma: public');

// Envoyer le fichier
readfile($filepath);
exit();
?>