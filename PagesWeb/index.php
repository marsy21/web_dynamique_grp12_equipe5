<?php
$database = "agora_francia";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);

$articles = [];

if ($db_found) {
    $sql = "SELECT a.id, a.nom, a.prix_initial, p.url 
            FROM articles a 
            JOIN photos p ON a.id = p.article_id 
            LIMIT 10";
    $result = mysqli_query($db_handle, $sql);

    while ($data = mysqli_fetch_assoc($result)) {
        $articles[] = $data;
    }
}
mysqli_close($db_handle);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Agora Francia</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    .wrapper {
      border: 2px solid black;
      width: 90%;
      margin: 20px auto;
    }

    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px;
      border-bottom: 2px solid #ccc;
    }

    header h1 {
      font-size: 2.5em;
    }

    header img {
      height: 60px;
    }

    nav {
      display: flex;
      justify-content: space-around;
      background-color: brown;
      padding: 10px;
      border-bottom: 2px solid #ccc;
    }

    nav a {
      background-color: burlywood;
      border: 1px solid beige;
      border-radius: 10px;
      padding: 10px 15px;
      text-decoration: none;
      color: #000;
      font-weight: bold;
      font-size: 1em;
    }

    section {
      text-align: center;
      color: brown;
      font-weight: bold;
    }

    #carrousel {
      position: relative;
      width: 100%;
      max-width: 600px;
      height: 300px;
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
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
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

    footer {
      border-top: 2px solid #ccc;
      font-size: 1em;
      color: brown;
      font-weight: bold;
      background-color: #f8f8ff;
      padding: 20px;
    }

    .footer-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .footer-left {
      text-align: left;
    }

    .footer-right img {
      height: 80px;
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
      <a href="#">Accueil</a>
      <a href="toutparcourir.php">Tout Parcourir</a>
      <a href="#">Notifications</a>
      <a href="#">Panier</a>
      <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
      <br>
      <p>Nous sommes le meilleur site de ventes vintage de toute la France. Vous pouvez vendre, acheter ou même devenir un de nos fournisseurs. Inscrivez-vous vite !!!</p>
      
      <!-- Carrousel -->
      <div id="carrousel">
        <ul>
          <?php foreach ($articles as $art): ?>
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

      <h2>Ventes flash</h2>
      <div class="ventes-flash-ligne">
        <?php foreach ($articles as $art): ?>
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
