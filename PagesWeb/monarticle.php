<?php
$database = "agora_francia";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

$article = null;

if ($db_found && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "
        SELECT a.nom, a.description, a.prix_initial, a.categorie_id, a.type_vente_id, a.qualite,a.date_publication,  p.url
        FROM articles a
        LEFT JOIN photos p ON a.id = p.article_id
        WHERE a.id = $id
        LIMIT 1
    ";
    $result = mysqli_query($db_handle, $sql);
    $article = mysqli_fetch_assoc($result);
}

mysqli_close($db_handle);
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
    }

    .image {
      flex: 1;
    }

    .image img {
      max-width: 100%;
      border-radius: 10px;
    }

    .info {
      flex: 2;
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
    .info .defaut,
    .info .date {
      font-size: 1em;
    }

    .info .defaut {
      color: red;
    }

    a.button {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 20px;
      background-color: brown;
      color: white;
      text-decoration: none;
      border-radius: 8px;
      font-weight: bold;
      text-align: center;
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
      <div class="description"><strong>Description :</strong> <?= nl2br(htmlspecialchars($article['description'])) ?></div>
      <div class="qualite"><strong>Qualité :</strong> <?= htmlspecialchars($article['qualite']) ?></div>
      <?php if (!empty($article['defaut'])): ?>
        <div class="defaut"><strong>Défauts :</strong> <?= htmlspecialchars($article['defaut']) ?></div>
      <?php endif; ?>
      <div class="date"><strong>Date de mise en vente :</strong> <?= htmlspecialchars($article['date_publication']) ?></div>
      <a href="toutparcourir.php" class="button">← Retour</a>
    </div>
  </div>
<?php else: ?>
  <p style="text-align:center; color:red;">Article introuvable.</p>
<?php endif; ?>

</body>
</html>
