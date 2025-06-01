<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$idUtilisateur = intval($_SESSION['utilisateur']['id']);

if (!isset($_GET['id'])) {
    die("Article non spécifié.");
}

$article_id = intval($_GET['id']);

// Récupérer l'article
$sql = "SELECT * FROM articles WHERE id = ?";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $article_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$article = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$article) {
    die("Article introuvable.");
}

// Récupérer la meilleure offre en enchère
$sql_max = "SELECT MAX(prix_max) as max_prix FROM encheres WHERE article_id = ?";
$stmt = mysqli_prepare($db, $sql_max);
mysqli_stmt_bind_param($stmt, "i", $article_id);
mysqli_stmt_execute($stmt);
$result_max = mysqli_stmt_get_result($stmt);
$max_row = mysqli_fetch_assoc($result_max);
mysqli_stmt_close($stmt);

$max_prix = $max_row['max_prix'] ?? $article['prix_initial'];

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['offre'])) {
    $offre = floatval($_POST['offre']);
    if ($offre <= $max_prix) {
        $message = "Votre offre doit être supérieure à la meilleure offre actuelle (" . number_format($max_prix, 2, ',', '') . " €).";
    } else {
        // Insérer la nouvelle enchère
        $sql_insert = "INSERT INTO encheres (article_id, client_id, prix_max) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($db, $sql_insert);
        mysqli_stmt_bind_param($stmt, "iid", $article_id, $idUtilisateur, $offre);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Offre enregistrée avec succès.";
            $max_prix = $offre;
        } else {
            $message = "Erreur lors de l'enregistrement de l'offre.";
        }
        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Meilleure Offre - <?= htmlspecialchars($article['nom']) ?></title>
    <link rel="stylesheet" href="style.css" />
    <style>
nav a[href="panier.php"] { 
            background-color: orange;
            color: white;
        }

     
      input[type=number] { padding: 8px; width: 150px; font-size: 1em; }
      button { padding: 8px 15px; font-size: 1em; background-color: orange; border: none; cursor: pointer; color: white; }
      .message { margin-top: 15px; font-weight: bold; color: green; }
      .error { color: red; }
      .info { margin-top: 10px; }
    </style>
</head>
<body>
<div class="wrapper">
    <header>
        <h1>Agora Francia</h1>
        <img src="Articles/Images/logo.png" alt="Logo Agora" style="height: 50px;">
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
        <h2>Meilleure Offre pour : <?= htmlspecialchars($article['nom']) ?></h2>
        <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($article['description'])) ?></p>
        <p><strong>Prix initial :</strong> <?= number_format($article['prix_initial'], 2, ',', '') ?> €</p>
        <p class="info">Meilleure offre actuelle : <strong><?= number_format($max_prix, 2, ',', '') ?> €</strong></p>

        <form method="post" action="">
            <label for="offre">Votre offre (en €) :</label><br>
            <input type="number" step="0.01" min="<?= $max_prix + 0.01 ?>" name="offre" id="offre" required><br><br>
            <button type="submit">Proposer une offre</button>
        </form>

        <?php if ($message): ?>
            <p class="message <?= strpos($message, 'Erreur') !== false || strpos($message, 'doit être') !== false ? 'error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>
    </section>

    <footer>
      <div class="footer-content" style="display:flex; justify-content:space-between; align-items:center;">
        <div>
          <p>📍 Agora Francia</p>
          <p>12 rue de Victor Hugo, 75015 Paris</p>
          <p>📞 01 23 45 67 89</p>
          <p>📧 contact@agorafrancia.fr</p>
        </div>
        <div>
          <img src="Articles/Images/logo.png" alt="Logo Agora" style="height: 40px;">
        </div>
      </div>
    </footer>
</div>
</body>
</html>
