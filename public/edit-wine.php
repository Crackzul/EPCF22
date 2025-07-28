<?php
require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$id = (int)$_GET['id'];

try {
    $pdo = new PDO('mysql:host=localhost;dbname=mycave_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer le vin existant
    $stmt = $pdo->prepare('SELECT * FROM wine WHERE id = ?');
    $stmt->execute([$id]);
    $wine = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$wine) {
        header('Location: dashboard.php');
        exit();
    }

    if ($request->getMethod() === 'POST') {
        $name = $request->request->get('name');
        $year = $request->request->get('year');
        $grapes = $request->request->get('grapes');
        $country = $request->request->get('country');
        $region = $request->request->get('region');
        $description = $request->request->get('description');
        $picture = $wine['picture'];
        
        // Gestion de l'upload d'image
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES['picture']['tmp_name'];
            $originalName = basename($_FILES['picture']['name']);
            $targetDir = __DIR__ . '/assets/img/';
            $targetFile = $targetDir . $originalName;
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
        
        $stmt = $pdo->prepare('UPDATE wine SET name=?, year=?, grapes=?, country=?, region=?, description=?, picture=? WHERE id=?');
        $stmt->execute([$name, $year, $grapes, $country, $region, $description, $picture, $id]);
        header('Location: dashboard.php');
        exit();
    }
} catch (PDOException $e) {
    $error = "Erreur lors de la modification du vin: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un vin – MyCave</title>
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="add-wine">
    <header class="add-wine-header">
        <div class="header-content">
            <img src="assets/img/logo-large.png" alt="myCAVE logo" class="logo">
            <div class="header-text">
                <h1>Modifier la bouteille</h1>
                <p>Modifiez les informations de votre vin</p>
            </div>
        </div>
    </header>

    <main class="add-wine-main">
        <div class="background-form">
            <img src="assets/img/pexels-finn-ruijter-2153149058-32906672.jpg" alt="Image cave" class="cave-image">
        </div>
        
        <div class="add-wine-modal">
            <div class="modal-header">
                <h2>Modifier la bouteille</h2>
                <button class="btn-back" onclick="window.location.href='dashboard.php'">
                    ← Retour
                </button>
            </div>
            
            <form class="add-wine-form" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Nom du vin</label>
                    <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($wine['name']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="year">Millésime</label>
                    <input type="number" id="year" name="year" required min="1900" max="2030" value="<?php echo htmlspecialchars($wine['year']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="grapes">Cépages</label>
                    <input type="text" id="grapes" name="grapes" required value="<?php echo htmlspecialchars($wine['grapes']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="country">Pays</label>
                    <input type="text" id="country" name="country" required value="<?php echo htmlspecialchars($wine['country']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="region">Région</label>
                    <input type="text" id="region" name="region" required value="<?php echo htmlspecialchars($wine['region']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($wine['description']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="picture">Image (fichier)</label>
                    <input type="file" id="picture" name="picture" accept="image/*">
                    <?php if ($wine['picture']): ?>
                        <div style="margin-top:8px;">Image actuelle : <img src="assets/img/<?php echo htmlspecialchars($wine['picture']); ?>" alt="" style="height:40px;vertical-align:middle;"> (<?php echo htmlspecialchars($wine['picture']); ?>)</div>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn-submit">Enregistrer les modifications</button>
            </form>
        </div>
    </main>

    <footer class="add-wine-footer">
        <p>© 2025 MyCave - Votre cave à vin digitale</p>
    </footer>
</body>
</html> 