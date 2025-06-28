<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Users/login.php');
    exit;
}

$docId = $_GET['id'] ?? null;
$categoryId = $_GET['category_id'] ?? null;

if (!$docId || !$categoryId) {
    $_SESSION['error'] = "Paramètres manquants";
    header("Location: view.php?category_id=$categoryId");
    exit;
}

// Vérifier que le document appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT path FROM files WHERE id = ? AND user_id = ? AND category_id = ?");
$stmt->execute([$docId, $_SESSION['user_id'], $categoryId]);
$file = $stmt->fetch();

if (!$file) {
    $_SESSION['error'] = "Document introuvable ou accès refusé";
    header("Location: view.php?category_id=$categoryId");
    exit;
}

// Supprimer le fichier physique
if (file_exists($file['path'])) {
    unlink($file['path']);
}

// Supprimer la ligne dans la base
$stmt = $pdo->prepare("DELETE FROM files WHERE id = ?");
$stmt->execute([$docId]);

$_SESSION['success'] = "Document supprimé avec succès";
header("Location: view.php?category_id=$categoryId");
exit;
