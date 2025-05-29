<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

include 'db.php';
$id = $_SESSION['utilisateur']['id'];
$utilisateur = $_SESSION['utilisateur'];

$edit_mode = $_GET['edit'] ?? null;
$message = "";

// Mise à jour (formulaire envoyé)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modifier_client'])) {
        $adresse1 = mysqli_real_escape_string($db, $_POST['adresse1'] ?? '');
        $ville = mysqli_real_escape_string($db, $_POST['ville'] ?? '');
        mysqli_query($db, "UPDATE clients SET adresse1='$adresse1', ville='$ville' WHERE id=$id");
        $message = "Informations client mises à jour.";
        header("Location: votrecompte.php");
        exit;
    }

    if (isset($_POST['modifier_vendeur'])) {
        $pseudo = mysqli_real_escape_string($db, $_POST['pseudo'] ?? '');
        mysqli_query($db, "UPDATE vendeurs SET pseudo='$pseudo' WHERE id=$id");
        $message = "Informations vendeur mises à jour.";
        header("Location: votrecompte.php");
        exit;
    }
}

// Récup infos après mise à jour
$res_client = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
$is_client = mysqli_num_rows($res_client) > 0;
$client_info = $is_client ? mysqli_fetch_assoc($res_client) : null;

$res_vendeur = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
$is_vendeur = mysqli_num_rows($res_vendeur) > 0;
$vendeur_info = $is_vendeur ? mysqli_fetch_assoc($res_vendeur) : null;
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Votre Compte</title>
  <link rel="stylesheet" href="style.css">

  <style>
    li {
  list-style-type: none;
}
nav a[href="votrecompte.php"] {
  background-color: orange;
  color: white;
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
      <a href="panier.php">Panier</a>
      <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
        <h1>Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?> <?= htmlspecialchars($utilisateur['nom']) ?></h1><br>
<p>Email : <?= htmlspecialchars($utilisateur['email']) ?></p>
<p>Date de création : <?= htmlspecialchars($utilisateur['date_creation']) ?></p>
<br>
<?php if ($message): ?>
    <p style="color:green;"><?= $message ?></p>
<?php endif; ?>

<?php if ($is_client): ?>
    <h3>Informations Client</h3>
    <?php if ($edit_mode === 'client'): ?>
        <form method="post">
            <label>Adresse : <input type="text" name="adresse1" value="<?= htmlspecialchars($client_info['adresse1']) ?>"></label><br>
            <label>Ville : <input type="text" name="ville" value="<?= htmlspecialchars($client_info['ville']) ?>"></label><br>
            <button type="submit" name="modifier_client">Sauvegarder</button>
            <a href="votrecompte.php"><button type="button">Annuler</button></a>
        </form>
    <?php else: ?>
        <ul>
            <li>Adresse : <?= htmlspecialchars($client_info['adresse1']) ?></li>
            <li>Ville : <?= htmlspecialchars($client_info['ville']) ?></li>
        </ul>
        <a href="?edit=client"><button>Modifier infos client</button></a>
    <?php endif; ?>
<?php else: ?>
    <p>Souhaitez-vous acheter ? <a href="devenirclient.php">Devenir Client</a></p>
<?php endif; ?>


<?php if ($is_vendeur): ?>
    <h3><br>Informations Vendeur</h3>
    <?php if ($edit_mode === 'vendeur'): ?>
        <form method="post">
            <label>Pseudo : <input type="text" name="pseudo" value="<?= htmlspecialchars($vendeur_info['pseudo']) ?>"></label><br>
            <button type="submit" name="modifier_vendeur">Sauvegarder</button>
            <a href="votrecompte.php"><button type="button">Annuler</button></a>
        </form>
    <?php else: ?>
        <ul>
            <li>Pseudo : <?= htmlspecialchars($vendeur_info['pseudo']) ?></li>
        </ul>
        <a href="?edit=vendeur"><button>Modifier infos vendeur</button></a>
        <h1><br>Vos articles en ventes : </h1>
        <?php
// Récupération des articles du vendeur
$res_articles = mysqli_query($db, "SELECT * FROM articles WHERE id_vendeur = $id");

if (mysqli_num_rows($res_articles) > 0): ?>
    <h3><br>Vos articles en vente :</h3>
    <div style="display:flex; flex-wrap:wrap; gap: 20px;">
        <?php while ($article = mysqli_fetch_assoc($res_articles)): ?>
            <div style="border:1px solid #ccc; padding:10px; width:250px;">
                <a href="article.php?id=<?= $article['id'] ?>">
                    <img src="<?= htmlspecialchars($article['image_url'] ?? 'Articles/Images/default.jpg') ?>" alt="Image" style="width:100%;">
                    <h4><?= htmlspecialchars($article['titre']) ?></h4>
                </a>
                <p>Prix : <?= htmlspecialchars($article['prix']) ?> €</p>
                <p>Type de vente : <?= htmlspecialchars($article['type_vente']) ?></p>
                <form method="post" action="supprimer_article.php" onsubmit="return confirm('Supprimer cet article ?');">
                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                    <button type="submit">🗑 Supprimer</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
<?php else: ?>
    <p><br>Vous n'avez publié aucun article pour le moment.</p>
<?php endif; ?>
<br>
<a href="ajouter_article.php"><button>➕ Ajouter un article</button></a>


    <?php endif; ?>
<?php else: ?>
    <p><br>Souhaitez-vous vendre ? <a href="devenirvendeur.php">Devenir Vendeur</a></p>
<?php endif; ?>
<br><br>

<form method="post" action="logout.php">
    <button type="submit">Se déconnecter</button>
</form>
     
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