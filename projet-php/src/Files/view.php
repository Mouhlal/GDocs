<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Users/login.php');
    exit;
}

$categoryId = $_GET['category_id'] ?? null;

// Vérification que la catégorie appartient à l'utilisateur
$stmt = $pdo->prepare("SELECT id, name FROM categories WHERE id = ? AND user_id = ?");
$stmt->execute([$categoryId, $_SESSION['user_id']]);
$category = $stmt->fetch();

if (!$category) {
    $_SESSION['error'] = "Catégorie introuvable";
    header('Location: ../../index.php');
    exit;
}

// Récupération des documents de la catégorie
$stmt = $pdo->prepare("SELECT id, name, upload_date FROM files WHERE category_id = ? AND user_id = ? ORDER BY upload_date DESC");
$stmt->execute([$categoryId, $_SESSION['user_id']]);
$documents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Documents - <?= htmlspecialchars($category['name']) ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .document-container {
            margin-top: 30px;
        }
        .document-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
        .document-item:hover {
            background-color: #f9f9f9;
        }
        .document-actions a {
            margin-left: 10px;
            color: #555;
        }
        .document-actions a:hover {
            color: #2196F3;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #555;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="../../index.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> Retour à l'accueil
        </a>
        
        <h1>
            <i class="fas fa-folder"></i> 
            <?= htmlspecialchars($category['name']) ?>
        </h1>
        <?php
// Au début du fichier après les requêtes SQL
$success = $_SESSION['success'] ?? null;
$error = $_SESSION['error'] ?? null;
unset($_SESSION['success'], $_SESSION['error']);
?>

<!-- Affichez les messages ici -->
<?php if ($success): ?>
<div class="alert alert-success">
    <?= htmlspecialchars($success) ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger">
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>
        <!-- Formulaire d'ajout de document -->
      <!-- Remplacez la section du formulaire dans view.php par ceci -->
<div class="upload-section">
   <form id="uploadForm" action="upload.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="category_id" value="<?= $categoryId ?>">
<input type="file" name="document" id="document" accept=".docx,.pdf,.pptx" required>
    <button type="submit">Importer</button>
</form>


</div>
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert error"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>
<script>
// Ajoutez ce script en bas de la page
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('document');
    
    if (fileInput.files.length === 0) {
        alert('Veuillez sélectionner un fichier');
        e.preventDefault();
    }
});
</script>
        
        <!-- Liste des documents -->
        <div class="document-container">
            <h3>
                <i class="fas fa-files"></i> 
                Documents (<?= count($documents) ?>)
            </h3>
            
            <?php if (count($documents) > 0): ?>
                <?php foreach ($documents as $doc): ?>
                    <div class="document-item">
                        <div class="document-info">
                            <i class="fas fa-file-word"></i>
                            <?= htmlspecialchars($doc['name']) ?>
                            <small><?= date('d/m/Y H:i', strtotime($doc['upload_date'])) ?></small>
                        </div>
                        
                       <div class="document-actions">
    <a href="download.php?id=<?= $doc['id'] ?>" title="Télécharger">
        <i class="fas fa-download"></i> Exporter
    </a>
    <a href="preview.php?id=<?= $doc['id'] ?>" title="Visualiser" target="_blank">
        <i class="fas fa-eye"></i> Voir
    </a>
    <a href="delete.php?id=<?= $doc['id'] ?>&category_id=<?= $categoryId ?>" title="Supprimer" onclick="return confirm('Voulez-vous vraiment supprimer ce document ?');" style="color: red;">
        <i class="fas fa-trash"></i> Supprimer
    </a>
</div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-message">Aucun document dans cette catégorie</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Afficher le nom du fichier sélectionné
        document.getElementById('document').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Aucun fichier sélectionné';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>
</html>