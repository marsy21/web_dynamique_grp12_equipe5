<?php
session_start();
include 'db.php';


$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } else {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

        if ($db) {
            $email = mysqli_real_escape_string($db, $email);
            $sql_check = "SELECT id FROM utilisateurs WHERE email = '$email'";
            $res_check = mysqli_query($db, $sql_check);

            if (mysqli_num_rows($res_check) > 0) {
                $erreur = "Cet email est déjà utilisé.";
            } else {
                $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) 
                        VALUES ('$nom', '$prenom', '$email', '$mdp_hash', 'client')";
                if (mysqli_query($db, $sql)) {
                    $id_utilisateur = mysqli_insert_id($db);
                    $sql_user = "SELECT * FROM utilisateurs WHERE id = $id_utilisateur";
                    $res_user = mysqli_query($db, $sql_user);
                    
                    if ($res_user && mysqli_num_rows($res_user) == 1) {
                        $_SESSION['utilisateur'] = mysqli_fetch_assoc($res_user);
                        header("Location: votrecompte.php");
                        exit;
                    } else {
                        $erreur = "Erreur lors de la récupération de vos informations.";
                    }
                } else {
                    $erreur = "Erreur lors de la création du compte.";
                }

            }
            mysqli_close($db);
        } else {
            $erreur = "Connexion à la base échouée.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>creationdecompte</title>
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
            <h1>Créer un compte</h1>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label><br>
        <label>Prénom : <input type="text" name="prenom" required></label><br>
        <label>Email : <input type="email" name="email" required></label><br>
        <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
        <button type="submit">Créer le compte</button>
    </form>
    <p style="color:red"><?= $erreur ?></p>
    <p>Vous avez deja un compte ? <a href="connexion.php"> Se connecter </a></p>

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