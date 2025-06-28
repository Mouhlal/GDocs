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

if (!$file) {
    $_SESSION['error'] = "Document introuvable";
    header('Location: ../../index.php');
    exit;
}

// Affichage selon le type de fichier
$extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

switch ($extension) {
    case 'pdf':
        header('Content-type: application/pdf');
        break;
    case 'doc':
    case 'docx':
        // Pour les fichiers Word, on propose le téléchargement
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file['name']) . '"');
        break;
    default:
        // Pour les autres types, affichage brut
        header('Content-Type: text/plain');
}

readfile($file['path']);
exit;
?>