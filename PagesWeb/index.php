<?php
session_start();
include 'db.php';

// Connexion utilisateur
$connect_msg = "Nous sommes le meilleur site de ventes vintage de toute la France.";
$is_client = $is_vendeur = false;
$pseudo_vendeur = null;

if (isset($_SESSION['utilisateur'])) {
    $id = $_SESSION['utilisateur']['id'];

    $res_client = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
    $is_client = mysqli_num_rows($res_client) > 0;

    $res_vendeur = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
    $is_vendeur = mysqli_num_rows($res_vendeur) > 0;
    $pseudo_vendeur = $is_vendeur ? mysqli_fetch_assoc($res_vendeur)['pseudo'] : null;

    $connect_msg = $is_client && $is_vendeur ? "Connecté Client et Vendeur $pseudo_vendeur" : 
                   ($is_client ? "Connecté Client" : "Connecté $pseudo_vendeur");
}

// Articles les plus récents pour le carrousel (9 derniers)
$carrousel_articles = [];
$sql_carrousel = "SELECT a.id, a.nom, a.prix_initial, p.url
                  FROM articles a
                  JOIN photos p ON a.id = p.article_id
                  ORDER BY a.date_publication DESC
                  LIMIT 9";
$res1 = mysqli_query($db, $sql_carrousel);
while ($row = mysqli_fetch_assoc($res1)) {
    $carrousel_articles[] = $row;
}

// Articles en vente immédiate pour les ventes flash
$ventes_flash = [];
$sql_flash = "SELECT a.id, a.nom, a.prix_initial, p.url
              FROM articles a
              JOIN photos p ON a.id = p.article_id
              WHERE a.type_vente = 'immediate'
              ORDER BY a.date_publication DESC
              LIMIT 9";
$res2 = mysqli_query($db, $sql_flash);
while ($row = mysqli_fetch_assoc($res2)) {
    $ventes_flash[] = $row;
}
mysqli_close($db);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Agora Francia</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="style.css">

  <style>
      nav a[href="index.php"] {
      background-color: orange;
      color: white;
    }


    #carrousel {
      position: relative;
      width: 100%;
      max-width: 800px;
      height: 500px;
      overflow: hidden;
      margin: 30px auto;
      border: 2px solid #ccc;
      display: flex;
      justify-content: center;
      align-items: center; 
    }

    #carrousel ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    #carrousel ul li {
      position: absolute;
      top: 0;
      left: 0;
      display: none;
      width: 100%;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    #carrousel ul li img {
    width: auto;
    height: 100%;
    max-height: 100%;
    object-fit: contain;
    display: block;
    margin: auto;
  }

    .controls {
      display: flex;
      justify-content: center;
      gap: 10px;
      margin-bottom: 30px;
    }

    .controls button {
      background-color: burlywood;
      color: black;
      border: none;
      padding: 5px 15px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 14px;
    }

    .controls button:hover {
      background-color: brown;
    }

    .ventes-flash-ligne {
      display: flex;
      justify-content: center;
      gap: 25px;
      margin: 20px auto 40px auto;
      padding: 10px;
      flex-wrap: wrap;
    }

    .ventes-flash-ligne .article {
      width: 160px;
      text-align: center;
      background-color: #fff8f8;
      border: 2px solid #ccc;
      border-radius: 10px;
      padding: 10px;
    }

    .ventes-flash-ligne .article img {
      width: 100%;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 5px;
    }

    .ventes-flash-ligne .article span {
      display: block;
      color: purple;
      font-weight: bold;
      font-size: 1.1em;
    }

    
  </style>

  <script>
    $(document).ready(function () {
      const $carrousel = $('#carrousel'),
        $img = $carrousel.find('ul li'),
        indexImg = $img.length - 1;
      let i = 0;
      let interval;

      $img.hide();
      $img.eq(i).show();

      function showImage(index) {
        $img.hide();
        $img.eq(index).fadeIn();
      }

      function nextImage() {
        i = (i + 1) > indexImg ? 0 : i + 1;
        showImage(i);
      }

      function prevImage() {
        i = (i - 1) < 0 ? indexImg : i - 1;
        showImage(i);
      }

      $('.next').click(function () {
        nextImage();
        restartAutoSlide();
      });

      $('.prev').click(function () {
        prevImage();
        restartAutoSlide();
      });

      function startAutoSlide() {
        interval = setInterval(nextImage, 4000);
      }

      function restartAutoSlide() {
        clearInterval(interval);
        startAutoSlide();
      }

      startAutoSlide();
    });
  </script>
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
  <br>
  <p><?= htmlspecialchars($connect_msg) ?></p>

  <!-- Nouveautés -->
  <h2>Nouveauté :</h2>
  <div id="carrousel">
    <ul>
      <?php foreach ($carrousel_articles as $art): ?>
        <li>
          <a href="monarticle.php?id=<?= $art['id'] ?>">
            <img src="Articles/Images/<?= htmlspecialchars($art['url']) ?>" alt="<?= htmlspecialchars($art['nom']) ?>">
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="controls">
    <button class="prev">⟨ Précédent</button>
    <button class="next">Suivant ⟩</button>
  </div>

  <!-- Ventes flash -->
  <h2>Ventes flash</h2>
  <div class="ventes-flash-ligne">
    <?php foreach ($ventes_flash as $art): ?>
      <div class="article">
        <a href="monarticle.php?id=<?= $art['id'] ?>">
          <img src="Articles/Images/<?= htmlspecialchars($art['url']) ?>" alt="<?= htmlspecialchars($art['nom']) ?>">
        </a>
        <span><?= number_format($art['prix_initial'], 2, ',', '') ?>€</span>
      </div>
    <?php endforeach; ?>
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
