<?php
require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

if ($request->getMethod() === 'POST') {
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=mycave_db', 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $name = $request->request->get('name');
        $year = $request->request->get('year');
        $grapes = $request->request->get('grapes');
        $country = $request->request->get('country');
        $region = $request->request->get('region');
        $description = $request->request->get('description');
        
        // Gestion de l'upload d'image
        $picture = 'generic.jpg';
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['picture']['tmp_name'];
            $originalName = basename($_FILES['picture']['name']);
            $targetDir = __DIR__ . '/assets/img/';
            $targetFile = $targetDir . $originalName;
            // On évite les collisions de nom
            $i = 1;
            $fileName = $originalName;
            while (file_exists($targetFile)) {
                $fileName = $i . '_' . $originalName;
                $targetFile = $targetDir . $fileName;
                $i++;
            }
            if (move_uploaded_file($tmpName, $targetFile)) {
                $picture = $fileName;
            }
        }
        
        $stmt = $pdo->prepare('INSERT INTO wine (name, year, grapes, country, region, description, picture) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $year, $grapes, $country, $region, $description, $picture]);
        
        header("Location: dashboard.php");
        exit();
        
    } catch (PDOException $e) {
        $error = "Erreur lors de l'ajout du vin: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un vin – MyCave</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="add-wine">
    <header class="add-wine-header">
        <div class="header-content">
            <img src="assets/img/logo-large.png" alt="myCAVE logo" class="logo">
            <div class="header-text">
                <h1>Ajouter une nouvelle bouteille</h1>
                <p>Complétez les informations de votre vin</p>
            </div>
        </div>
    </header>

    <main class="add-wine-main">
        <div class="background-form">
            <img src="assets/img/pexels-finn-ruijter-2153149058-32906672.jpg" alt="Image cave" class="cave-image">
        </div>
        
        <div class="add-wine-modal">
            <div class="modal-header">
                <h2>Nouvelle bouteille</h2>
                <button class="btn-back" onclick="window.location.href='dashboard.php'">
                    ← Retour
                </button>
            </div>
            
            <form class="add-wine-form" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nom du vin</label>
                    <input type="text" id="name" name="name" required placeholder="Ex: Château Margaux">
                </div>
                
                <div class="form-group">
                    <label for="year">Millésime</label>
                    <input type="number" id="year" name="year" required placeholder="Ex: 2015" min="1900" max="2030">
                </div>
                
                <div class="form-group">
                    <label for="grapes">Cépages</label>
                    <input type="text" id="grapes" name="grapes" required placeholder="Ex: Cabernet Sauvignon, Merlot">
                </div>
                
                <div class="form-group">
                    <label for="country">Pays</label>
                    <input type="text" id="country" name="country" required placeholder="Ex: France">
                </div>
                
                <div class="form-group">
                    <label for="region">Région</label>
                    <input type="text" id="region" name="region" required placeholder="Ex: Bordeaux">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Notes sur le vin, arômes, etc."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="picture">Image (fichier)</label>
                    <input type="file" id="picture" name="picture" accept="image/*">
                </div>
                
                <button type="submit" class="btn-submit">Ajouter le vin</button>
            </form>
        </div>
    </main>

    <footer class="add-wine-footer">
        <p>© 2025 MyCave - Votre cave à vin digitale</p>
    </footer>
</body>
</html> 