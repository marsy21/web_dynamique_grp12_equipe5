<?php
session_start();
include 'db.php';  // ta connexion mysqli dans $db

$article = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Requête adaptée avec les colonnes utilisées précédemment
    $sql = "
        SELECT a.nom, a.description, a.prix_initial, a.categorie, a.rarete AS qualite, a.type_vente, a.date_publication, p.url
        FROM articles a
        LEFT JOIN photos p ON a.id = p.article_id
        WHERE a.id = $id
        LIMIT 1
    ";

    $result = mysqli_query($db, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $article = mysqli_fetch_assoc($result);
    }
}

// Fonction pour vérifier si connecté ET client
function estClient($db) {
    if (!isset($_SESSION['utilisateur']['id'])) {
        return false;
    }
    $id = intval($_SESSION['utilisateur']['id']);
    $res = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
    return ($res && mysqli_num_rows($res) > 0);
}

$client = estClient($db);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Détail de l'article</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background-color: #f5f5f5;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: white;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      display: flex;
      gap: 20px;
      flex-wrap: wrap;
    }

    .image {
      flex: 1 1 300px;
    }

    .image img {
      max-width: 100%;
      border-radius: 10px;
    }

    .info {
      flex: 2 1 500px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .info h2 {
      margin-bottom: 10px;
    }

    .info .prix {
      font-size: 1.5em;
      color: purple;
      font-weight: bold;
    }

    .info .description,
    .info .qualite,
    .info .categorie,
    .info .type_vente,
    .info .date {
      font-size: 1em;
    }

    a.button, button.action-button {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: brown;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      text-align: center;
      cursor: pointer;
      border: none;
      font-size: 1em;
      width: fit-content;
    }
  </style>
</head>
<body>

<?php if ($article): ?>
  <div class="container">
    <div class="image">
      <img src="Articles/Images/<?= $article['url'] ? htmlspecialchars($article['url']) : 'default.jpg' ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
    </div>
    <div class="info">
      <h2><?= htmlspecialchars($article['nom']) ?></h2>
      <div class="prix"><?= number_format($article['prix_initial'], 2, ',', '') ?> €</div>
      <div class="description"><strong>Description :</strong><br><?= nl2br(htmlspecialchars($article['description'])) ?></div>
      <div class="qualite"><strong>Rareté / Qualité :</strong> <?= htmlspecialchars($article['qualite']) ?></div>
      <div class="categorie"><strong>Catégorie :</strong> <?= htmlspecialchars($article['categorie']) ?></div>
      <div class="type_vente"><strong>Type de vente :</strong> <?= htmlspecialchars($article['type_vente']) ?></div>
      <div class="date"><strong>Date de mise en vente :</strong> <?= htmlspecialchars($article['date_publication']) ?></div>

      <?php
        // Déterminer le texte du bouton selon type_vente
        $typeVente = strtolower($article['type_vente']);
        $btnText = "Acheter";  // par défaut

        if ($typeVente === 'meilleure offre') {
            $btnText = "Enchérir";
        } elseif ($typeVente === 'negociation') {
            $btnText = "Négocier";
        } elseif ($typeVente === 'immediate') {
            $btnText = "Acheter";
        } else {
            // autre type ? On peut masquer ou mettre un texte générique
            $btnText = "Action";
        }

        // URL cible selon si client ou non
        if ($client) {
            $urlCible = "panier.php?id=$id";
        } else {
            $urlCible = "votrecompte.php";
        }
      ?>

      <button class="action-button" onclick="location.href='<?= $urlCible ?>'"><?= $btnText ?></button>

      <a href="toutparcourir.php" class="button">← Retour</a>
    </div>
  </div>
<?php else: ?>
  <p style="text-align:center; color:red;">Article introuvable.</p>
<?php endif; ?>

</body>
</html>
