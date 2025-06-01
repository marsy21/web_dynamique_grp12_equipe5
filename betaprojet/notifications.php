<?php
session_start();
include 'db.php';
require_once 'includes/fonctions/notifications.php';
require_once 'includes/fonctions/negociations.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

$id_vendeur = $_SESSION['utilisateur']['id'];

// Récupérer les offres de négociation en cours
$sql = "
    SELECT n.*, a.nom AS nom_article, u.prenom, u.nom AS nom_user, a.id AS article_id
    FROM negociations n
    JOIN articles a ON n.article_id = a.id
    JOIN utilisateurs u ON n.acheteur_id = u.id
    WHERE n.vendeur_id = ? AND n.statut = 'en cours'
    ORDER BY n.date_offre DESC
";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $id_vendeur);
$stmt->execute();
$offres = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Notifications - Agora Francia</title>
    <link rel="stylesheet" href="style.css">
    <style>
        nav a[href="notifications.php"] {
            background-color: orange;
            color: white;
        }
        .offre-box {
            border: 1px solid #aaa;
            border-radius: 8px;
            padding: 15px;
            margin: 15px auto;
            max-width: 700px;
            background: #fff7f7;
        }
        form {
            display: inline;
        }
        button {
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 6px;
        }
        .accepter { background-color: green; color: white; }
        .refuser { background-color: red; color: white; }
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
        <a href="notifications.php">Notifications</a>
        <a href="panier.php">Panier</a>
        <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
        <h2>Vos offres de négociation</h2>

        <?php if ($offres->num_rows === 0): ?>
            <p>Aucune négociation en cours.</p>
        <?php else: ?>
            <?php while ($offre = $offres->fetch_assoc()): ?>
                <div class="offre-box">
                    <p>
                        <strong><?= htmlspecialchars($offre['prenom'] . ' ' . $offre['nom_user']) ?></strong> propose 
                        <strong><?= number_format($offre['offre'], 2, ',', '') ?> €</strong> pour 
                        <strong><?= htmlspecialchars($offre['nom_article']) ?></strong>
                    </p>
                    <p>Tour : <?= $offre['tour'] ?>/5</p>

                    <form method="post" action="traiter_offre.php">
                        <input type="hidden" name="offre_id" value="<?= $offre['id'] ?>">
                        <input type="hidden" name="acheteur_id" value="<?= $offre['acheteur_id'] ?>">
                        <input type="hidden" name="article_id" value="<?= $offre['article_id'] ?>">
                        <button name="action" value="accepter" class="accepter">Accepter</button>
                        <button name="action" value="refuser" class="refuser">Refuser</button>
                    </form>
                </div>
            <?php endwhile; ?>
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
