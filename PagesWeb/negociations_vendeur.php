<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$idVendeur = intval($_SESSION['utilisateur']['id']);
$idArticle = intval($_GET['id'] ?? 0);

// Vérifier que l'article appartient bien au vendeur et est en "negociation"
$stmt = mysqli_prepare($db, "SELECT id FROM articles WHERE id = ? AND id_vendeur = ? AND type_vente = 'negociation'");
mysqli_stmt_bind_param($stmt, 'ii', $idArticle, $idVendeur);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    // Pas d'article ou pas propriétaire, ou mauvais type de vente
    header('Location: mesarticles.php');
    exit;
}

mysqli_stmt_close($stmt);

// Récupérer toutes les négociations pour cet article, avec info client
$sql = "
    SELECT n.id, n.prix_propose, n.date_negociation, n.tour, n.statut, u.nom, u.prenom
    FROM negociations n
    LEFT JOIN utilisateurs u ON n.client_id = u.id
    WHERE n.article_id = ? AND n.vendeur_id = ?
    ORDER BY n.date_negociation DESC, n.tour ASC
";

$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, 'ii', $idArticle, $idVendeur);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$negociations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $negociations[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Négociation - <?= htmlspecialchars($article['nom']) ?></title>
    <link rel="stylesheet" href="style.css" />
    <style>
        nav a[href="mesarticles.php"] { 
            background-color: orange;
            color: white;
        }
      input[type=number] { padding: 8px; width: 150px; font-size: 1em; }
      button { padding: 8px 15px; font-size: 1em; background-color: orange; border: none; cursor: pointer; color: white; }
      .message { margin-top: 15px; font-weight: bold; color: green; }
      .error { color: red; }
      table { width: 100%; border-collapse: collapse; margin-top: 15px; }
      th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
      .role-client { color: blue; font-weight: bold; }
      .role-vendeur { color: green; font-weight: bold; }
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
        <a href="votrecompte.php">Votre Compte</a>
        <a href="mesarticles.php">Mes Articles</a>
    </nav>

    <section>
       
<h1>Négociations pour l'article #<?= $idArticle ?></h1>

<?php if (empty($negociations)): ?>
    <p>Aucune négociation enregistrée pour cet article.</p>
<?php else: ?>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>Client</th>
                <th>Prix proposé (€)</th>
                <th>Date</th>
                <th>Tour</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($negociations as $neg): ?>
                <tr>
                    <td><?= htmlspecialchars(($neg['nom'] ?? 'Anonyme') . ' ' . ($neg['prenom'] ?? '')) ?></td>
                    <td><?= number_format($neg['prix_propose'], 2, ',', ' ') ?></td>
                    <td><?= htmlspecialchars($neg['date_negociation']) ?></td>
                    <td><?= intval($neg['tour']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($neg['statut'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
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
