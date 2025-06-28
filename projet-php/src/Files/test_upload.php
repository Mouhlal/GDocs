<?php
// test_upload.php
echo "<h2>Test Environnement</h2>";

// 1. Test des permissions
$uploadDir = __DIR__ . '/uploads/';
echo "Dossier upload: " . realpath($uploadDir) . "<br>";
echo "Existe: " . (file_exists($uploadDir) ? 'OUI' : 'NON') . "<br>";
echo "Accessible en écriture: " . (is_writable($uploadDir) ? 'OUI' : 'NON') . "<br>";

// 2. Test configuration PHP
echo "<h3>Configuration PHP</h3>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";

// 3. Test d'écriture
$testFile = $uploadDir . 'test_' . time() . '.txt';
if (file_put_contents($testFile, "Test")) {
    echo "<p style='color:green'>Test d'écriture réussi!</p>";
    unlink($testFile);
} else {
    echo "<p style='color:red'>Échec d'écriture</p>";
}
?>