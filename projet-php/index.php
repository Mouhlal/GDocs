<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

// Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: src/Users/login.php');
    exit;
}

// Récupération des catégories de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY name ASC");
$stmt->execute([$_SESSION['user_id']]);
$categories = $stmt->fetchAll();

// Récupération des derniers fichiers ajoutés
$stmt = $pdo->prepare("SELECT f.id, f.name, f.upload_date, c.name as category_name 
                      FROM files f 
                      JOIN categories c ON f.category_id = c.id 
                      WHERE f.user_id = ? 
                      ORDER BY f.upload_date DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$recent_files = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Documents</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- En-tête -->
        <header class="dashboard-header">
            <h1>Gestion des Documents</h1>
            <div class="user-menu">
                <span class="welcome-msg">Bienvenue, <?= htmlspecialchars($_SESSION['username']) ?> !</span>
                <a href="src/Users/logout.php" class="logout-btn">
    <i class="fas fa-sign-out-alt"></i> Déconnexion
</a>

            </div>
        </header>

        <!-- Section principale -->
        <main class="dashboard-main">
            <!-- Section des catégories -->
            <section class="categories-section">
                <div class="section-header">
                    <h2><i class="fas fa-folder"></i> Vos Catégories</h2>
                    <a href="src/Categories/add.php" class="add-btn">
                        <i class="fas fa-plus"></i> Nouvelle catégorie
                    </a>
                </div>

                <div class="categories-grid">
                    <?php if (count($categories) > 0): ?>
                        <?php foreach ($categories as $category): ?>
                            <div class="category-card">
                                <div class="category-icon">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h3><?= htmlspecialchars($category['name']) ?></h3>
                               <!-- Modifiez seulement cette partie dans votre fichier index.php -->
<div class="category-actions">
    <a href="src/Files/view.php?category_id=<?= $category['id'] ?>" class="action-btn">
        <i class="fas fa-eye"></i> Voir les documents
    </a>
   
</div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-folder-open fa-3x"></i>
                            <p>Aucune catégorie créée</p>
                            <a href="src/Categories/add.php" class="add-btn">
                                <i class="fas fa-plus"></i> Créer votre première catégorie
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Section des fichiers récents -->
            <section class="recent-files">
                <h2><i class="fas fa-clock"></i> Fichiers Récents</h2>
                
                <?php if (count($recent_files) > 0): ?>
                    <table class="files-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_files as $file): ?>
                                <tr>
                                    <td><?= htmlspecialchars($file['name']) ?></td>
                                    <td><?= htmlspecialchars($file['category_name']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($file['upload_date'])) ?></td>
                                    <td class="actions">
                                        <a href="src/Files/download.php?id=<?= $file['id'] ?>" title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <a href="src/Files/preview.php?id=<?= $file['id'] ?>" title="Visualiser">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-file-alt fa-3x"></i>
                        <p>Aucun fichier récent</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
</body>
</html>