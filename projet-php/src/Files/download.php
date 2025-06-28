<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header('Location: ../Users/login.php');
    exit;
}

$fileId = $_GET['id'];

// Vérification des permissions
$stmt = $pdo->prepare("SELECT f.path, f.name FROM files f 
                      JOIN categories c ON f.category_id = c.id 
                      WHERE f.id = ? AND f.user_id = ?");
$stmt->execute([$fileId, $_SESSION['user_id']]);
$file = $stmt->fetch();

if (!$file || !file_exists($file['path'])) {
    $_SESSION['error'] = "Fichier introuvable";
    header('Location: ../../index.php');
    exit;
}

// Forcer le téléchargement
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($file['name']) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file['path']));
readfile($file['path']);
exit;
?>