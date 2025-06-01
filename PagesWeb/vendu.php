<?php
session_start();
include 'db.php';

// Redirection si l'utilisateur n'est pas connecté
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

// Récupération des infos de session
$idVendeur = $_SESSION['utilisateur']['id'];
$utilisateur = $_SESSION['utilisateur'];

// Requête pour récupérer les commandes liées aux articles vendus par ce vendeur
$sql = "SELECT c.*, a.nom AS nom_article, a.prix_initial, u.nom AS acheteur_nom, u.prenom AS acheteur_prenom
        FROM commandes c
        JOIN articles a ON c.article_id = a.id
        JOIN utilisateurs u ON c.client_id = u.id
        WHERE a.id_vendeur = ?
        ORDER BY c.date_commande DESC";

$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idVendeur);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Votre Compte</title>
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
        <h2>Articles vendus</h2>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="commande">
                    <strong><?= htmlspecialchars($row['nom_article']) ?></strong><br>
                    Prix : <?= htmlspecialchars($row['prix_final']) ?> €<br>
                    Acheté par : <?= htmlspecialchars($row['acheteur_prenom']) ?> <?= htmlspecialchars($row['acheteur_nom']) ?><br>
                    Date : <?= htmlspecialchars($row['date_commande']) ?><br>
                    Type de vente : <?= htmlspecialchars($row['type_vente']) ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Aucun article vendu pour le moment.</p>
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
