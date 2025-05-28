<?php
$database = "agora_francia";
$db_handle = mysqli_connect('localhost', 'root', '');
$db_found = mysqli_select_db($db_handle, $database);
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
      margin-top: 20px;
    }

    .articles-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      padding: 20px;
    }

    .article-card {
      width: 180px;
      text-align: center;
      background: #fff0f5;
      border: 1px solid #ddd;
      border-radius: 10px;
      padding: 10px;
      transition: transform 0.2s ease;
    }

    .article-card:hover {
      transform: scale(1.05);
    }

    .article-card img {
      width: 100%;
      height: auto;
      border-radius: 5px;
    }

    .article-card .nom {
      margin-top: 10px;
      font-weight: bold;
    }

    .article-card .prix {
      color: #800080;
      font-weight: bold;
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
      <a href="#">Panier</a>
      <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
      <p>Nous sommes le meilleur site de ventes vintage de toute la France. Découvrez nos trésors !</p>
    </section>

    <div class="articles-container">
      <?php
      if ($db_found) {
          $sql = "
              SELECT a.id, a.nom, a.prix_initial, p.url 
              FROM articles a
              JOIN photos p ON a.id = p.article_id
              LIMIT 18
          ";
          $result = mysqli_query($db_handle, $sql);

          while ($data = mysqli_fetch_assoc($result)) {
              $id = $data['id'];
              $nom = $data['nom'];
              $prix = number_format($data['prix_initial'], 2, ',', '') . "€";
              $img_path = "Articles/Images/" . $data['url'];

              echo "<a href='monarticle.php?id=$id' class='article-card'>";
              echo "<img src='$img_path' alt='" . htmlspecialchars($nom) . "' />";
              echo "<div class='nom'>" . htmlspecialchars($nom) . "</div>";
              echo "<div class='prix'>$prix</div>";
              echo "</a>";
          }
      } else {
          echo "<p>Erreur de connexion à la base de données.</p>";
      }

      mysqli_close($db_handle);
      ?>
    </div>

    <footer>
      <div class="footer-content">
        <div class="footer-left">
          <p>📍 Agora Franciaaaaaa bebe mama</p>
          <p>12 rue de Victor Hugo, 75015 Paris</p>
          <p>📞 01 23 45 67 89</p>
          <p>📧 contact@agorafrancia.fr</p>
        </div>
        <div class="footer-right">
          <img src="Articles/Images/logo.png" alt="Logo Agora" />
        </div>
      </div>
    </footer>
  </div>
</body>
</html>
