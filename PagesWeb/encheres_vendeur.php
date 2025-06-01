<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$idVendeur = intval($_SESSION['utilisateur']['id']);
$idArticle = intval($_GET['id'] ?? 0);

// Vérifier que l'article appartient bien au vendeur et est en "meilleure offre"
$stmt = mysqli_prepare($db, "SELECT id FROM articles WHERE id = ? AND id_vendeur = ? AND type_vente = 'meilleure offre'");
mysqli_stmt_bind_param($stmt, 'ii', $idArticle, $idVendeur);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) === 0) {
    // Pas d'article ou pas propriétaire, ou mauvais type de vente
    header('Location: mesarticles.php');
    exit;
}

mysqli_stmt_close($stmt);

// Récupérer toutes les enchères pour cet article, avec info client (nom/prenom)
$sql = "
    SELECT e.id, e.prix_max, e.date_enchere, u.nom, u.prenom
    FROM encheres e
    LEFT JOIN utilisateurs u ON e.client_id = u.id
    WHERE e.article_id = ?
    ORDER BY e.prix_max DESC, e.date_enchere ASC
";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, 'i', $idArticle);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$encheres = [];
while ($row = mysqli_fetch_assoc($result)) {
    $encheres[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Enchères pour l'article #<?= htmlspecialchars($idArticle) ?></title>
    <link rel="stylesheet" href="style.css" />
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
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
        <h2>Enchères pour l'article #<?= htmlspecialchars($idArticle) ?></h2>

        <?php if (empty($encheres)): ?>
            <p>Aucune enchère enregistrée pour cet article.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Nom du client</th>
                        <th>Prénom du client</th>
                        <th>Prix proposé (€)</th>
                        <th>Date de l'enchère</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($encheres as $enchere): ?>
                        <tr>
                            <td><?= htmlspecialchars($enchere['nom'] ?? 'Anonyme') ?></td>
                            <td><?= htmlspecialchars($enchere['prenom'] ?? '') ?></td>
                            <td><?= number_format($enchere['prix_max'], 2, ',', ' ') ?></td>
                            <td><?= htmlspecialchars($enchere['date_enchere']) ?></td>
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
