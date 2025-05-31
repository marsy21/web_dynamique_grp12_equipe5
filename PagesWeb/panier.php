<?php
session_start();
include 'db.php';  // ta connexion mysqli dans $db

// Vérifier que l'utilisateur est connecté et client
if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: votrecompte.php');
    exit;
}
if ($_SESSION['utilisateur']['role'] !== 'client') {
    // Connecte mais pas client
    header('Location: votrecompte.php'); 
    exit;
}

$client_id = intval($_SESSION['utilisateur']['id']);
if (isset($_GET['id'])) {
    $article_id = intval($_GET['id']);

    // Vérifier que l'article n'est pas déjà dans le panier (optionnel)
    $check = mysqli_query($db, "SELECT * FROM panier WHERE client_id = $client_id AND article_id = $article_id");
    if (mysqli_num_rows($check) === 0) {
        $sql = "INSERT INTO panier (client_id, article_id) VALUES ($client_id, $article_id)";
        mysqli_query($db, $sql);
    }
    // Après ajout, rediriger vers panier sans paramètre pour éviter doublons si refresh
    header("Location: panier.php");
    exit;
}
// Gérer la suppression d'article si demandé via GET ou POST
if (isset($_GET['supprimer'])) {
    $article_id_suppr = intval($_GET['supprimer']);
    $sql_suppr = "DELETE FROM panier WHERE client_id = $client_id AND article_id = $article_id_suppr LIMIT 1";
    mysqli_query($db, $sql_suppr);
    header("Location: panier.php");
    exit;
}

// Récupérer tous les articles dans le panier du client avec détails
$sql = "
    SELECT p.article_id, a.nom, a.prix_initial, a.type_vente, ph.url
    FROM panier p
    JOIN articles a ON p.article_id = a.id
    LEFT JOIN photos ph ON a.id = ph.article_id
    WHERE p.client_id = $client_id
    GROUP BY p.article_id
";


$result = mysqli_query($db, $sql);

$articles = [
    'immediate' => [],
    'meilleure offre' => [],
    'negociation' => []
];

$total = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $type = strtolower($row['type_vente']);
        if (!isset($articles[$type])) $articles[$type] = [];
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
        a.supprimer:hover { text-decoration: underline; }
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
        .btn-payer:hover { background-color: #a0522d; }
    </style>
</head>
<body>
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
        <a href="panier.php">Panier</a>
        <a href="votrecompte.php">Votre Compte</a>
        <a href="mesarticles.php">Mes Articles</a>
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
            <h2><?= $label ?></h2>
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
                                    <a href="encherir.php?id=<?= $art['article_id'] ?>" class="btn-payer">Enchérir</a>
                                <?php elseif ($type === 'negociation'): ?>
                                    <a href="negocier.php?id=<?= $art['article_id'] ?>" class="btn-payer">Négocier</a>
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
        <a href="paiement.php" class="btn-payer">Procéder au paiement total immediat</a>
    <?php endif; ?>

    <br><br>
    <a href="toutparcourir.php">← Continuer vos achats</a>
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
