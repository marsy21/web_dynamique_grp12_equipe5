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

// Récupérer la dernière négociation entre ce client et le vendeur pour cet article
$sql_neg = "SELECT * FROM negociations WHERE article_id = ? AND (client_id = ? OR vendeur_id = ?) ORDER BY date_negociation DESC LIMIT 1";
$stmt = mysqli_prepare($db, $sql_neg);
mysqli_stmt_bind_param($stmt, "iii", $article_id, $idUtilisateur, $idUtilisateur);
mysqli_stmt_execute($stmt);
$result_neg = mysqli_stmt_get_result($stmt);
$last_neg = mysqli_fetch_assoc($result_neg);
mysqli_stmt_close($stmt);

// Récupérer le vendeur de l'article
$vendeur_id = $article['id_vendeur'];

// Déterminer si l'utilisateur est vendeur ou client dans la négociation
// Si l'utilisateur est vendeur sur cet article => $estVendeur = true
$estVendeur = ($idUtilisateur === $vendeur_id);

// Gestion du tour : on alterne tour 1 client, 2 vendeur, etc.
// On part du dernier tour +1
$tour = 1;
if ($last_neg) {
    $tour = $last_neg['tour'] + 1;
}

// Message d'erreur ou succès
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prix_propose'])) {
    $prix_propose = floatval($_POST['prix_propose']);

    // On vérifie que le prix proposé est cohérent (par exemple >0)
    if ($prix_propose <= 0) {
        $message = "Le prix proposé doit être supérieur à 0.";
    } else {
        // Insertion de la négociation avec statut en_cours
        $sql_insert = "INSERT INTO negociations (article_id, client_id, vendeur_id, prix_propose, tour, statut) VALUES (?, ?, ?, ?, ?, 'en_cours')";
        
        // client_id et vendeur_id selon rôle
        if ($estVendeur) {
            $client_id = $last_neg ? $last_neg['client_id'] : 0; // si pas d'historique, 0 par défaut
            $vendeur_id_insert = $idUtilisateur;
        } else {
            $client_id = $idUtilisateur;
            $vendeur_id_insert = $vendeur_id;
        }

        $stmt = mysqli_prepare($db, $sql_insert);
        mysqli_stmt_bind_param($stmt, "iiiid", $article_id, $client_id, $vendeur_id_insert, $prix_propose, $tour);

        if (mysqli_stmt_execute($stmt)) {
            $message = "Offre de négociation enregistrée.";
            // Mettre à jour le tour pour la prochaine proposition
            $tour++;
        } else {
            $message = "Erreur lors de l'enregistrement.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Récupérer tout l'historique des négociations pour affichage
$sql_historique = "SELECT n.*, u1.nom AS nom_client, u1.prenom AS prenom_client, u2.nom AS nom_vendeur, u2.prenom AS prenom_vendeur 
                   FROM negociations n 
                   LEFT JOIN utilisateurs u1 ON n.client_id = u1.id
                   LEFT JOIN utilisateurs u2 ON n.vendeur_id = u2.id
                   WHERE n.article_id = ? ORDER BY n.date_negociation ASC";

$stmt = mysqli_prepare($db, $sql_historique);
mysqli_stmt_bind_param($stmt, "i", $article_id);
mysqli_stmt_execute($stmt);
$result_historique = mysqli_stmt_get_result($stmt);

$negociations = [];
while ($row = mysqli_fetch_assoc($result_historique)) {
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
        nav a[href="panier.php"] { 
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
        <a href="mesarticles.php">Mes Articles</a>
        <a href="notification.php">Notifications</a>
        <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
        <h2>Négociation pour : <?= htmlspecialchars($article['nom']) ?></h2>
        <p><strong>Description :</strong> <?= nl2br(htmlspecialchars($article['description'])) ?></p>

        <form method="post" action="">
            <label for="prix_propose">Votre proposition (en €) :</label><br>
            <input type="number" step="0.01" min="0.01" name="prix_propose" id="prix_propose" required><br><br>
            <button type="submit">Envoyer la proposition</button>
        </form>

        <?php if ($message): ?>
            <p class="message <?= strpos($message, 'Erreur') !== false || strpos($message, 'doit être') !== false ? 'error' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <h3>Historique des négociations</h3>
        <?php if (count($negociations) === 0): ?>
            <p>Aucune négociation enregistrée.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Vendeur</th>
                        <th>Prix proposé (€)</th>
                        <th>Tour</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($negociations as $neg): ?>
                        <tr>
                            <td><?= htmlspecialchars($neg['date_negociation']) ?></td>
                            <td class="role-client"><?= htmlspecialchars($neg['prenom_client'] . ' ' . $neg['nom_client']) ?></td>
                            <td class="role-vendeur"><?= htmlspecialchars($neg['prenom_vendeur'] . ' ' . $neg['nom_vendeur']) ?></td>
                            <td><?= number_format($neg['prix_propose'], 2, ',', '') ?></td>
                            <td><?= intval($neg['tour']) ?></td>
                            <td><?= htmlspecialchars($neg['statut']) ?></td>
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
