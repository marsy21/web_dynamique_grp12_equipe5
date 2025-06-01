<?php 
session_start();


$redirect = $_GET['redirect'] ?? null;

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

include 'db.php';
$id = $_SESSION['utilisateur']['id'];
$utilisateur = $_SESSION['utilisateur'];

$edit_mode = $_GET['edit'] ?? null;
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modifier_client'])) {
        $adresse1 = mysqli_real_escape_string($db, $_POST['adresse1'] ?? '');
        $ville = mysqli_real_escape_string($db, $_POST['ville'] ?? '');
        mysqli_query($db, "UPDATE clients SET adresse1='$adresse1', ville='$ville' WHERE id=$id");
        header("Location: votrecompte.php");
        exit;
    }

    if (isset($_POST['modifier_vendeur'])) {
        $pseudo = mysqli_real_escape_string($db, $_POST['pseudo'] ?? '');
        mysqli_query($db, "UPDATE vendeurs SET pseudo='$pseudo' WHERE id=$id");
        header("Location: votrecompte.php");
        exit;
    }
}

$res_client = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
$is_client = mysqli_num_rows($res_client) > 0;
$client_info = $is_client ? mysqli_fetch_assoc($res_client) : null;

$res_vendeur = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
$is_vendeur = mysqli_num_rows($res_vendeur) > 0;
$vendeur_info = $is_vendeur ? mysqli_fetch_assoc($res_vendeur) : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Votre Compte</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .messageredirect {

  
    background: linear-gradient(45deg,rgb(234, 255, 0),rgb(77, 255, 160));
    font-weight: bold;
    border-radius: 12px;

    }


    li { list-style-type: none; }
    nav a[href="votrecompte.php"] {
      background-color: orange;
      color: white;
    }
    .bloc-client, .bloc-vendeur {
      border: 1px solid #ccc;
      padding: 15px;
      margin-top: 20px;
      border-radius: 10px;
      background-color: #f9f9f9;
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
        <a href="panier.php">Panier</a>
        <a href="mesarticles.php">Mes Articles</a>
        <a href="notification.php">Notifications</a>
        <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
      <h1>Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?> <?= htmlspecialchars($utilisateur['nom']) ?></h1><br>
      <p>Email : <?= htmlspecialchars($utilisateur['email']) ?></p>
      <p>Date de création : <?= htmlspecialchars($utilisateur['date_creation']) ?></p>
      <br>
<?php if ($utilisateur && $utilisateur['role'] === 'admin'): ?>
    <div style="background-color: #f39c12; color: white; padding: 10px; text-align: center; font-weight: bold;">
      Connecté en tant qu'administrateur
    </div><a href="admin.php"><button type="button">Les comptes Utilisateur</button></a>


  <?php endif; ?>
      <?php if ($message): ?>
        <p style="color:green;"><?= $message ?></p>
      <?php endif; ?>

      <?php if ($is_client): ?>
        <div class="bloc-client">
          <h3>Informations Client</h3>
          <?php if ($edit_mode === 'client'): ?>
            <form method="post">
              <label>Adresse : <input type="text" name="adresse1" value="<?= htmlspecialchars($client_info['adresse1']) ?>"></label><br>
              <label>Ville : <input type="text" name="ville" value="<?= htmlspecialchars($client_info['ville']) ?>"></label><br>
              <button type="submit" name="modifier_client">Sauvegarder</button>
              <a href="votrecompte.php"><button type="button">Annuler</button></a>
            </form>
          <?php else: ?>
            <ul>
              <li>Adresse : <?= htmlspecialchars($client_info['adresse1']) ?></li>
              <li>Ville : <?= htmlspecialchars($client_info['ville']) ?></li>
            </ul>
            <a href="?edit=client"><button>Modifier infos client</button></a>
            <a href="panier.php"><button>🛒 Mon panier</button></a>
<a href="achete.php"><button>Mes Depenses</button></a>

            <?php if (isset($_GET['paiement']) && $_GET['paiement'] === 'success'): ?>
            <div class="success-message">🎉 Paiement effectué avec succès !</div>
          <?php endif; ?>

          <?php endif; ?>
        </div>
    <?php else: ?>
  <?php if ($redirect === 'panier'): ?>
    <p class="messageredirect">Souhaitez-vous acheter ? <a href="devenirclient.php">Devenir Client</a></p>
  <?php else: ?>
    <p>Souhaitez-vous acheter ? <a href="devenirclient.php">Devenir Client</a></p>
  <?php endif; ?>
<?php endif; ?>


      <?php if ($is_vendeur): ?>
        <div class="bloc-vendeur">
          <h3>Informations Vendeur</h3>
          <?php if ($edit_mode === 'vendeur'): ?>
            <form method="post">
              <label>Pseudo : <input type="text" name="pseudo" value="<?= htmlspecialchars($vendeur_info['pseudo']) ?>"></label><br>
              <button type="submit" name="modifier_vendeur">Sauvegarder</button>
              <a href="votrecompte.php"><button type="button">Annuler</button></a>
            </form>
          <?php else: ?>
            <ul>
              <li>Pseudo : <?= htmlspecialchars($vendeur_info['pseudo']) ?></li>
            </ul>
            <a href="?edit=vendeur"><button>Modifier infos vendeur</button></a>
            <a href="mesarticles.php"><button> Mes articles en vente</button></a>
            <a href="vendu.php"><button>Mes Ventes</button></a>

          <?php endif; ?>
        </div>
      <?php else: ?>
  <?php if ($redirect === 'mesarticles'): ?>
    <p class="messageredirect"><br>Souhaitez-vous vendre ? <a href="devenirvendeur.php">Devenir Vendeur</a></p>
</p>
  <?php else: ?>
    <p><br>Souhaitez-vous vendre ? <a href="devenirvendeur.php">Devenir Vendeur</a></p>
  <?php endif; ?>
<?php endif; ?>


      <br><br>
      <form method="post" action="logout.php">
        <button type="submit">Se déconnecter</button>
      </form>
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
