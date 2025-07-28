<?php
require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$request = Request::createFromGlobals();
$session = new Session(new NativeSessionStorage());

// Connexion à la base de données
try {
    $pdo = new PDO('mysql:host=localhost;dbname=mycave_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupérer tous les vins de la base de données
    $stmt = $pdo->query('SELECT * FROM wine ORDER BY id DESC');
    $wines = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $wines = [];
    $error = "Erreur de connexion à la base de données: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Tableau de bord – MyCAVE</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard">
  <header class="dashboard-header">
    <div class="header-content">
      <img src="assets/img/logo-large.png" alt="myCAVE logo" class="logo">
      <div class="header-text">
        <h1>Bienvenue dans votre cave Didier</h1>
        <p>Elle contient déjà <span id="bottle-count"><?php echo count($wines); ?></span> bouteilles</p>
      </div>
      <div class="header-actions">
        <button class="btn-primary" onclick="window.location.href='add-wine.php'">Ajouter une nouvelle bouteille</button>
        <button class="btn-outline" onclick="window.location.href='login.php'">Déconnexion</button>
      </div>
    </div>
  </header>

  <main class="dashboard-main">
    <div class="background-cave">
      <img src="assets/img/pexels-finn-ruijter-2153149058-32906672.jpg" alt="Image cave" class="cave-image">
    </div>
    <div class="wines-container" id="wines-container">
      
      <?php if (empty($wines)): ?>
        <div class="no-wines">
          <p>Aucun vin dans votre cave pour le moment.</p>
          <p>Cliquez sur "Ajouter une nouvelle bouteille" pour commencer !</p>
        </div>
      <?php else: ?>
        <?php foreach ($wines as $wine): ?>
          <div class="wine-card">
            <div class="wine-image">
              <img src="assets/img/<?php echo $wine['picture'] ?: 'generic.jpg'; ?>" alt="<?php echo htmlspecialchars($wine['name']); ?>">
            </div>
                               <div class="wine-info">
                     <h3><?php echo htmlspecialchars($wine['name']); ?></h3>
                     <div class="wine-details">
                       <span class="year"><?php echo htmlspecialchars($wine['year']); ?></span>
                       <span class="grape"><?php echo htmlspecialchars($wine['grapes']); ?></span>
                       <span class="country"><?php echo htmlspecialchars($wine['country']); ?></span>
                       <span class="region"><?php echo htmlspecialchars($wine['region']); ?></span>
                     </div>
                     <p class="wine-description">
                       <?php if ($wine['description']): ?>
                         <?php echo htmlspecialchars($wine['description']); ?>
                       <?php endif; ?>
                     </p>
              <div class="wine-actions">
                <button class="btn-icon" title="Modifier" onclick="window.location.href='edit-wine.php?id=<?php echo $wine['id']; ?>'">
                  <img src="assets/img/pen-to-square.svg" alt="">
                </button>
                <button class="btn-icon" title="Supprimer" onclick="if(confirm('Supprimer ce vin ?')) window.location.href='delete-wine.php?id=<?php echo $wine['id']; ?>';">
                  <img src="assets/img/trash-arrow-up.svg" alt="">
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>

    </div>
  </main>

  <footer class="dashboard-footer">
    <p>© 2025 Marty Didier tous droits réservés</p>
  </footer>

  <script>
    function updateBottleCount() {
      const wineCards = document.querySelectorAll('.wine-card');
      const count = wineCards.length;
      const countElement = document.getElementById('bottle-count');
      if (countElement) {
        countElement.textContent = count;
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      updateBottleCount();
    });
  </script>

</body>
</html> 