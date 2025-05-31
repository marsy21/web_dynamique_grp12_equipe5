<?php

session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$idUtilisateur = intval($_SESSION['utilisateur']['id']);

// Vérifie s'il est dans la table vendeurs
$stmt = mysqli_prepare($db, "SELECT id FROM vendeurs WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $idUtilisateur);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $estVendeur);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$estVendeur) {
    // Pas vendeur, donc redirection
    header('Location: votrecompte.php?redirect=mesarticles');
    exit;
}

$id_vendeur = $idUtilisateur;

if (isset($_GET['supprimer'])) {
    $article_id = intval($_GET['supprimer']);
    mysqli_query($db, "DELETE FROM articles WHERE id = $article_id AND id_vendeur = $id_vendeur");
    header("Location: mesarticles.php");
    exit;
}

// Récupérer tous les articles du vendeur avec photo
$sql = "
    SELECT a.id, a.nom, a.prix_initial, a.type_vente, ph.url
    FROM articles a
    LEFT JOIN photos ph ON a.id = ph.article_id
    WHERE a.id_vendeur = $id_vendeur
    GROUP BY a.id
";
$result = mysqli_query($db, $sql);

$articles = [
    'immediate' => [],
    'meilleure offre' => [],
    'negociation' => []
];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $type = strtolower($row['type_vente']);
        $articles[$type][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Mes Articles - Agora Francia</title>
    <link rel="stylesheet" href="style.css">
    <style>
    

        nav a[href="mesarticles.php"] { background-color: orange; color: white; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
        img { max-width: 80px; border-radius: 6px; }
        .prix { color: purple; font-weight: bold; }
        .btn-action {
            background-color: brown;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        a.supprimer { color: red; text-decoration: none; font-weight: bold; }

        
    
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
        <a href="panier.php">Panier</a>
        <a href="votrecompte.php">Votre Compte</a>
        <a href="mesarticles.php">Mes Articles</a>
    </nav>

    <section>
        <h1>Mes Articles en Vente</h1>

        <?php if (array_sum(array_map('count', $articles)) === 0): ?>
            <p>Vous n'avez encore publié aucun article.</p>
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
                                    <a href="monarticle.php?id=<?= intval($art['id']) ?>">
                                        <img src="Articles/Images/<?= htmlspecialchars($art['url'] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($art['nom']) ?>" />
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($art['nom']) ?></td>
                                <td class="prix"><?= number_format($art['prix_initial'], 2, ',', '') ?> €</td>
                                <td>
                                    <?php if ($type === 'immediate'): ?>
                                        <span style="color:gray;">Aucune action</span>
                                    <?php elseif ($type === 'meilleure offre'): ?>
                                        <a href="encheres_vendeur.php?id=<?= $art['id'] ?>" class="btn-action">Voir enchères</a>
                                    <?php elseif ($type === 'negociation'): ?>
                                        <a href="negociations_vendeur.php?id=<?= $art['id'] ?>" class="btn-action">Voir négociations</a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                <a class="supprimer" href="mesarticles.php?supprimer=<?= intval($art['id']) ?>" onclick="return confirm('Supprimer cet article ?');">X</a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <a href="ajouter_article.php" >➕ Ajouter un article</a>

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
