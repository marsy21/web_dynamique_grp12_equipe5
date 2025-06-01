<?php
session_start();
include 'db.php';  // Connexion dans $db

$article = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Récupération de toutes les infos + toutes les photos
    $sql = "
        SELECT a.nom, a.description, a.prix_initial, a.categorie, a.rarete AS qualite, a.type_vente, a.date_publication, p.url
        FROM articles a
        LEFT JOIN photos p ON a.id = p.article_id
        WHERE a.id = $id
    ";

    $result = mysqli_query($db, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $photos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            if (!$article) {
                $article = [
                    'nom' => $row['nom'],
                    'description' => $row['description'],
                    'prix_initial' => $row['prix_initial'],
                    'categorie' => $row['categorie'],
                    'qualite' => $row['qualite'],
                    'type_vente' => $row['type_vente'],
                    'date_publication' => $row['date_publication'],
                    'photos' => []
                ];
            }
            if ($row['url']) {
                $article['photos'][] = $row['url'];
            }
        }
    }
}

// Vérifie si utilisateur connecté est un client
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
  <link rel="stylesheet" href="style.css">
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

    .image-carousel {
      position: relative;
      width: 100%;
      max-width: 400px;
      height: 300px;
      overflow: hidden;
    }

    .image-carousel img {
      position: absolute;
      top: 0;
      left: 0;
      opacity: 0;
      transition: opacity 0.8s ease;
      width: 100%;
      height: 100%;
      object-fit: contain;
      border-radius: 10px;
    }

    .image-carousel img.active {
      opacity: 1;
      position: relative;
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
    <?php if ($article): ?>
    <div class="container">
      <div class="image">
        <?php if (count($article['photos']) > 1): ?>
          <div class="image-carousel">
            <?php foreach ($article['photos'] as $url): ?>
              <img src="Articles/Images/<?= htmlspecialchars($url) ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
            <?php endforeach; ?>
             <button id="prev-btn" style="position:absolute; top:50%; left:10px; transform:translateY(-50%); z-index:10;">❮</button>
  <button id="next-btn" style="position:absolute; top:50%; right:10px; transform:translateY(-50%); z-index:10;">❯</button>
          </div>
        <?php else: ?>
          <img src="Articles/Images/<?= htmlspecialchars($article['photos'][0] ?? 'default.jpg') ?>" alt="<?= htmlspecialchars($article['nom']) ?>">
        <?php endif; ?>
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
          $typeVente = strtolower($article['type_vente']);
          $btnText = "Acheter";
          if ($typeVente === 'meilleure offre') {
              $btnText = "Enchérir";
          } elseif ($typeVente === 'negociation') {
              $btnText = "Négocier";
          }
          $urlCible = $client ? "panier.php?id=$id" : "votrecompte.php";
        ?>

        <button class="action-button" onclick="location.href='<?= $urlCible ?>'"><?= $btnText ?></button>
        <a href="toutparcourir.php" class="button">← Retour</a>
      </div>
    </div>
    <?php else: ?>
      <p style="text-align:center; color:red;">Article introuvable.</p>
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

<script>

  document.addEventListener('DOMContentLoaded', function () {
    const images = document.querySelectorAll('.image-carousel img');
    let current = 0;

    if (images.length > 1) {
      images[current].classList.add('active');

      const prevBtn = document.getElementById('prev-btn');
      const nextBtn = document.getElementById('next-btn');

      prevBtn.addEventListener('click', () => {
        images[current].classList.remove('active');
        current = (current - 1 + images.length) % images.length;
        images[current].classList.add('active');
      });

      nextBtn.addEventListener('click', () => {
        images[current].classList.remove('active');
        current = (current + 1) % images.length;
        images[current].classList.add('active');
      });
    }
  });

</script>

</body>
</html>
