<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Users/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $userId = $_SESSION['user_id'];
    
    try {
        $stmt = $pdo->prepare("INSERT INTO categories (name, user_id) VALUES (?, ?)");
        $stmt->execute([$name, $userId]);
        header('Location: ../../index.php');
    } catch (PDOException $e) {
        $error = "Erreur lors de la création : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nouvelle Catégorie</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Créer une catégorie</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Nom de la catégorie" required>
            <button type="submit">Créer</button>
            <a href="../../index.php">Annuler</a>
        </form>
    </div>
</body>
</html>