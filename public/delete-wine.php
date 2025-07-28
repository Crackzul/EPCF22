<?php
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = (int)$_GET['id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mycave_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('DELETE FROM wine WHERE id = ?');
    $stmt->execute([$id]);
} catch (PDOException $e) {
    // Optionnel : log ou afficher l'erreur
}

header('Location: dashboard.php');
exit(); 