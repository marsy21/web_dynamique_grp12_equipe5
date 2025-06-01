<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

$idClient = $_SESSION['utilisateur']['id'];

// Requête pour récupérer les commandes où le client est celui connecté
$sql = "SELECT c.*, a.nom AS nom_article, a.prix_initial, u.nom AS vendeur_nom, u.prenom AS vendeur_prenom
        FROM commandes c
        JOIN articles a ON c.article_id = a.id
        JOIN utilisateurs u ON a.id_vendeur = u.id
        WHERE c.client_id = ?
        ORDER BY c.date_commande DESC";

$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idClient);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Achats</title>
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
        <a href="panier.php">Panier</a>
        <a href="mesarticles.php">Mes Articles</a>
        <a href="notification.php">Notifications</a>
        <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section class="commandes">
        <h2>Mes achats</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="commande">
                    <strong><?= htmlspecialchars($row['nom_article']) ?></strong><br>
                    Prix payé : <?= htmlspecialchars($row['prix_final']) ?> €<br>
                    Vendeur : <?= htmlspecialchars($row['vendeur_prenom']) ?> <?= htmlspecialchars($row['vendeur_nom']) ?><br>
                    Date : <?= htmlspecialchars($row['date_commande']) ?><br>
                    Type de vente : <?= htmlspecialchars($row['type_vente']) ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Vous n'avez encore rien acheté.</p>
        <?php endif; ?>
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
