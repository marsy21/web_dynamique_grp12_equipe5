<?php
session_start();
$erreur = '';
include 'db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    if (empty($email) || empty($mdp)) {
        $erreur = "Tous les champs doivent être remplis.";
    } else {

        if ($db) {
            $email = mysqli_real_escape_string($db, $email);
            $query = "SELECT * FROM utilisateurs WHERE email = '$email'";
            $result = mysqli_query($db, $query);

            if ($user = mysqli_fetch_assoc($result)) {
                if (password_verify($mdp, $user['mot_de_passe'])) {
                    $_SESSION['utilisateur'] = $user;
                    header("Location: votrecompte.php");
                    exit;
                } else {
                    $erreur = "Mot de passe incorrect.";
                }
            } else {
                $erreur = "Aucun utilisateur trouvé avec cet email.";
            }

            mysqli_close($db);
        } else {
            $erreur = "Erreur de connexion à la base de données.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Agora Francia</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="style.css">
  <style>
    nav a[href="votrecompte.php"] {
  background-color: orange;
  color: white;
}
  </style>

</head>

<body>
  <div class="wrapper">
    <header>
      <h1>Agora Francia</h1>
      <img src="Articles/Images/logo.png" alt="Logo Agora">
    </header>

    <nav>
      <a href="index.php">Accueil</a>
      <a href="toutparcourir.php">Tout Parcourir</a>
      <a href="#">Notifications</a>
      <a href="#">Panier</a>
      <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
        <h1>Connexion</h1>
    <form method="post">
        <label>Email : <input type="email" name="email" required></label><br>
        <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
        <button type="submit">Se connecter</button>
    </form>
    <p style="color:red"><?= $erreur ?></p>
    <p>Pas encore de compte ? <a href="creationdecompteu.php">Créer un compte</a></p>

    </section>

    <footer>
      <div class="footer-content">
        <div class="footer-left">
          <p>📍 Agora Francia</p>
          <p>12 rue de Victor Hugo, 75015 Paris</p>
          <p>📞 01 23 45 67 89</p>
          <p>📧 contact@agorafrancia.fr</p>
        </div>
        <div class="footer-right">
          <img src="Articles/Images/logo.png" alt="Logo Agora">
        </div>
      </div>
    </footer>
  </div>
</body>
</html>