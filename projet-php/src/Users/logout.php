<?php
session_start();

// Détruit toutes les variables de session
$_SESSION = [];

// Détruit la session côté serveur
session_destroy();

// Redirige vers la page de login (ou la page d’accueil)
header('Location: login.php');
exit();
