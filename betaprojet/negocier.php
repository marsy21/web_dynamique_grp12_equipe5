<?php
session_start();
include 'db.php';
require_once 'includes/fonctions/notifications.php';
require_once 'includes/fonctions/negociations.php';

// Vérifier utilisateur connecté
if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

$acheteur_id = $_SESSION['utilisateur']['id'];
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Récupérer l’article
$req = $db->prepare("SELECT a.*, v.id AS vendeur_id, v.pseudo, ph.url 
                     FROM articles a
                     JOIN vendeurs v ON a.id_vendeur = v.id
                     LEFT JOIN photos ph ON a.id = ph.article_id
                     WHERE a.id = ?");
$req->bind_param("i", $article_id);
$req->execute();
$article = $req->get_result()->fetch_assoc();

if (!$article) {
    echo "Article introuvable.";
    exit;
}

// Dernière offre
$derniere_offre = get_derniere_offre($db, $article_id, $acheteur_id);
$tour_actuel = $derniere_offre ? $derniere_offre['tour'] + 1 : 1;
$statut = $derniere_offre['statut'] ?? 'en cours';

// Gérer la soumission du formulaire
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tour_actuel <= 5 && $statut === 'en cours') {
    $offre = floatval($_POST['offre']);
    enregistrer_offre_negociation($db, $article_id, $acheteur_id, $article['vendeur_id'], $offre, $tour_actuel);

    $notif = "Nouvelle offre de négociation : $offre € pour l'article '" . $article['nom'] . "'.";
    envoyer_notification($db, $article['vendeur_id'], $notif);

    header("Location: votrecompte.php"); // rediriger pour éviter double soumission
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Négociation - <?= htmlspecialchars($article['nom']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .negociation-box {
            width: 50%;
            margin: auto;
            padding: 20px;
            background-color: #f8f0ff;
            border-radius: 10px;
            margin-top: 40px;
        }
        input[type="number"] {
            padding: 8px;
            width: 100%;
            margin-top: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: purple;
            color: white;
            border: none;
            margin-top: 15px;
            cursor: pointer;
            border-radius: 8px;
        }
        .image-preview {
            width: 200px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="negociation-box">
    <h2>Négocier : <?= htmlspecialchars($article['nom']) ?></h2>
    <img class="image-preview" src="Articles/Images/<?= htmlspecialchars($article['url'] ?? 'default.jpg') ?>" alt="Article">
    <p><strong>Prix initial :</strong> <?= number_format($article['prix_initial'], 2, ',', '') ?> €</p>
    <p><strong>Tour :</strong> <?= $tour_actuel ?>/5</p>
    <p><strong>Vendeur :</strong> <?= htmlspecialchars($article['pseudo']) ?></p>

    <?php if ($tour_actuel > 5): ?>
        <p style="color:red;"><strong>Nombre maximal d’échanges atteint. Négociation terminée.</strong></p>
    <?php elseif ($statut !== 'en cours'): ?>
        <p style="color:blue;"><strong>Cette négociation est terminée (statut : <?= htmlspecialchars($statut) ?>).</strong></p>
    <?php else: ?>
        <form method="post">
            <label>Votre offre (€) :</label>
            <input type="number" name="offre" step="0.01" required>
            <button type="submit">Envoyer l’offre</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
