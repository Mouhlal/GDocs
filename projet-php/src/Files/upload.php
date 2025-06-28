<?php
// Affichage des erreurs pour le debug (à désactiver en production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../../includes/config.php';
require_once '../../includes/auth.php';
require_once '../../includes/upload.php';

// Vérifie que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit();
}

// Vérifie que la méthode est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['category_id'] ?? null;
    $file = $_FILES['document'] ?? null;

    // Vérifie que la catégorie appartient à l'utilisateur
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE id = ? AND user_id = ?");
    $stmt->execute([$categoryId, $_SESSION['user_id']]);

    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = "Catégorie invalide";
        header("Location: ../../index.php");
        exit();
    }

    // Validation du fichier
if (!isValidFile($file)) {
    $_SESSION['error'] = "Seuls les fichiers DOCX, PDF et PPTX sont autorisés";
    header("Location: view.php?category_id=$categoryId");
    exit();
}


    // Préparation du dossier
    $uploadDir = "../../uploads/$categoryId/";
    if (!createUploadDirectory($uploadDir)) {
        $_SESSION['error'] = "Impossible de créer le dossier d'upload";
        header("Location: view.php?category_id=$categoryId");
        exit();
    }

    // Sauvegarde du fichier
    $targetPath = saveUploadedFile($file, $uploadDir);
    if (!$targetPath) {
        $_SESSION['error'] = "Erreur lors du déplacement du fichier";
        header("Location: view.php?category_id=$categoryId");
        exit();
    }

    // Enregistrement en base de données
    try {
        $stmt = $pdo->prepare("INSERT INTO files (name, path, category_id, user_id, upload_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$file['name'], $targetPath, $categoryId, $_SESSION['user_id']]);

        $_SESSION['success'] = "Fichier importé avec succès !";
    } catch (PDOException $e) {
        // Supprime le fichier si l'insert échoue
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        $_SESSION['error'] = "Erreur base de données : " . $e->getMessage();
    }

    header("Location: view.php?category_id=$categoryId");
    exit();
} else {
    $_SESSION['error'] = "Requête invalide";
    header("Location: ../../index.php");
    exit();
}
