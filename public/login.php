<?php
require_once '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

$request = Request::createFromGlobals();
$session = new Session(new NativeSessionStorage());

if ($request->getMethod() === 'POST') {
    $email = $request->request->get('email');
    $password = $request->request->get('password');
    
    // Test simple - si email = didier@example.com et password = supersecret
    if ($email === 'didier@example.com' && $password === 'supersecret') {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Identifiants incorrects";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Connexion - MyCAVE</title>
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="login-page">

  <header class="header-login">
    <img src="assets/img/logo-large.png" alt="myCAVE logo" class="logo">
    <div class="login-title">
      <h1>Avec <strong>myCAVE</strong></h1>
      <p>Bienvenue dans votre cave</p>
    </div>
  </header>

  <main class="login-main">
    <div class="login-card">
      <img src="assets/img/pexels-hugoml-6314361.jpg" alt="Image cave" class="login-image">
      <form class="login-form" method="POST">
        <h2>Se connecter</h2>
        <?php if (isset($error)): ?>
          <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <button type="submit">Connexion</button>
      </form>
    </div>
  </main>

  <footer class="footer-login">
    <p>© 2025 MyCAVE Didier - tous droits réservés</p>
  </footer>

</body>
</html> 