<?php

session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$idUtilisateur = intval($_SESSION['utilisateur']['id']);


$stmt = mysqli_prepare($db, "SELECT id FROM clients WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $idUtilisateur);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $estClient);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$estClient) {
    // Pas vendeur, donc redirection
    header('Location: votrecompte.php?redirect=panier');
    exit;
}

$client_id = $idUtilisateur;

if (isset($_GET['id'])) {
    $article_id = intval($_GET['id']);

    // Vérifier que l'article n'est pas déjà dans le panier
    $stmt_check = mysqli_prepare($db, "SELECT 1 FROM panier WHERE client_id = ? AND article_id = ?");
    mysqli_stmt_bind_param($stmt_check, "ii", $client_id, $article_id);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);
    $exists = mysqli_stmt_num_rows($stmt_check) > 0;
    mysqli_stmt_close($stmt_check);

    if (!$exists) {
        $stmt_insert = mysqli_prepare($db, "INSERT INTO panier (client_id, article_id) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt_insert, "ii", $client_id, $article_id);
        mysqli_stmt_execute($stmt_insert);
        mysqli_stmt_close($stmt_insert);
    }
    // Après ajout, rediriger vers panier sans paramètre pour éviter doublons si refresh
    header("Location: panier.php");
    exit;
}

// Gérer la suppression d'article si demandé via GET
if (isset($_GET['supprimer'])) {
    $article_id_suppr = intval($_GET['supprimer']);
    $stmt_suppr = mysqli_prepare($db, "DELETE FROM panier WHERE client_id = ? AND article_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt_suppr, "ii", $client_id, $article_id_suppr);
    mysqli_stmt_execute($stmt_suppr);
    mysqli_stmt_close($stmt_suppr);
    header("Location: panier.php");
    exit;
}

$sql = "
    SELECT p.article_id, a.nom, a.prix_initial, LOWER(a.type_vente) AS type_vente, ph.url,
           e.terminee, e.client_id AS enchere_client_id
    FROM panier p
    JOIN articles a ON p.article_id = a.id
    LEFT JOIN photos ph ON a.id = ph.article_id
    LEFT JOIN encheres e ON e.article_id = a.id AND e.terminee = 1 AND e.client_id = ?
    WHERE p.client_id = ?
    GROUP BY p.article_id
";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "ii", $client_id, $client_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);




$articles = [
    'immediate' => [],
    'meilleure offre' => [],
    'negociation' => []
];

$total = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $type = strtolower($row['type_vente']);
        if (!isset($articles[$type])) {
            $articles[$type] = [];
        }
        $articles[$type][] = $row;

        if ($type === 'immediate') {
            $total += $row['prix_initial'];
        }
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Mon Panier - Agora Francia</title>
    <link rel="stylesheet" href="style.css">

    <style>
        nav a[href="panier.php"] { 
            background-color: orange;
            color: white;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
        img { max-width: 80px; border-radius: 6px; }
        .prix { color: purple; font-weight: bold; }
        .total { text-align: right; font-size: 1.3em; margin-top: 15px; }
        a.supprimer { color: red; text-decoration: none; font-weight: bold; }
        .btn-payer {
            margin-top: 20px;
            background-color: brown;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        .btn-payer1 {
            margin-top: 20px;
            background-color: lightblue;
            color: Green;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        .btn-payer:hover { background-color: #a0522d; }
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
    <div class="container">
    <h1>Mon Panier</h1>
    <?php if (array_sum(array_map('count', $articles)) === 0): ?>
        <p>Votre panier est vide.</p>
        <a href="toutparcourir.php">← Retour aux articles</a>
    <?php else: ?>

        <?php foreach (['immediate' => 'Achat Immédiat', 'meilleure offre' => 'Meilleure Offre', 'negociation' => 'Négociation'] as $type => $label): ?>
            <?php if (!empty($articles[$type])): ?>
                <h2><?= htmlspecialchars($label) ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Nom</th>
                            <th>Prix</th>
                            <th>Action</th>
                            <th>Supprimer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles[$type] as $art): ?>
                            <tr>
                                <td>
                                    <a href="monarticle.php?id=<?= intval($art['article_id']) ?>">
                                        <img src="Articles/Images/<?= htmlspecialchars($art['url'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($art['nom']) ?>" />
                                    </a>
                                </td>
                                <td>
                                    <a href="monarticle.php?id=<?= intval($art['article_id']) ?>" style="text-decoration: none; color: inherit;">
                                        <?= htmlspecialchars($art['nom']) ?>
                                    </a>
                                </td>

                                <td class="prix"><?= number_format($art['prix_initial'], 2, ',', '') ?> €</td>
                                <td>
                                    <?php if ($type === 'immediate'): ?>
                                        <a href="paiement.php" class="btn-payer">Procéder au paiement</a>
                                   <?php elseif ($type === 'meilleure offre'): ?>
                                    <?php if ($art['terminee'] == 1 && $art['enchere_client_id'] == $client_id): ?>
                                                <a href="meilleuroffre.php?id=<?= intval($art['article_id']) ?>" class="btn-payer1">Choisi</a>
                                    <?php else: ?>
                                        <a href="meilleuroffre.php?id=<?= intval($art['article_id']) ?>" class="btn-payer">Enchérir</a>
                                    <?php endif; ?>
                                    <?php elseif ($type === 'negociation'): ?>
                                        <a href="negociation.php?id=<?= $art['article_id'] ?>" class="btn-payer">Négocier</a>
                                    <?php endif; ?>
                                </td>
                                <td><a class="supprimer" href="panier.php?supprimer=<?= intval($art['article_id']) ?>" onclick="return confirm('Supprimer cet article ?');">X</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (!empty($articles['immediate'])): ?>
            <div class="total"><strong>Total immédiat : <?= number_format($total, 2, ',', '') ?> €</strong></div>
            <a href="paiement.php" class="btn-payer">Procéder au paiement total immédiat</a>
        <?php endif; ?>

        <br><br>
        <a href="toutparcourir.php">← Continuer vos achats</a>
    <?php endif; ?>
    </div>
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
