<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

include 'db.php';
$id_utilisateur = $_SESSION['utilisateur']['id'];

// Vérifie que l'utilisateur est bien un vendeur
$res_vendeur = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id_utilisateur");
if (mysqli_num_rows($res_vendeur) === 0) {
    echo "Vous devez être un vendeur pour ajouter un article.";
    exit;
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = mysqli_real_escape_string($db, $_POST['nom'] ?? '');
    $description = mysqli_real_escape_string($db, $_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $type_vente = mysqli_real_escape_string($db, $_POST['type_vente'] ?? '');

    // Téléversement image (simplifié)
    $image_url = 'Articles/Images/default.jpg';
    if (!empty($_FILES['image']['tmp_name'])) {
        $upload_dir = 'Articles/Images/';
        $filename = basename($_FILES['image']['name']);
        $target_path = $upload_dir . time() . '_' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = $target_path;
        }
    }

    // Insertion dans la base
    $query = "INSERT INTO articles (nom, description, prix, type_vente, id_vendeur, image_url) 
              VALUES ('$nom', '$description', $prix, '$type_vente', $id_utilisateur, '$image_url')";
    if (mysqli_query($db, $query)) {
        $message = "Article ajouté avec succès.";
    } else {
        $message = "Erreur lors de l'ajout de l'article : " . mysqli_error($db);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Ajouter un Article - Agora Francia</title>
    <link rel="stylesheet" href="style.css">
    <style>
        nav a[href="ajouter_article.php"] { background-color: orange; color: white; }
        form { max-width: 600px; margin: 20px auto; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="text"], input[type="number"], textarea, select {
            width: 100%; padding: 8px; margin-top: 5px; border-radius: 4px; border: 1px solid #ccc;
        }
        button { margin-top: 20px; background-color: brown; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; }
        .error { color: red; }
        .success { color: green; font-weight: bold; }
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
        <a href="mesarticles.php">Mes Articles</a>
        <a href="ajouter_article.php">Ajouter un article</a>
    </nav>

    <section>
        <h1>Ajouter un Article</h1>
        
         <?php if ($message): ?>
                <p style="color:green;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label>Nom : <input type="text" name="nom" required></label><br><br>
                <label>Description :<br><textarea name="description" rows="5" cols="40" required></textarea></label><br><br>
                <label>Prix (€) : <input type="number" name="prix" step="0.01" required></label><br><br>
                <label>Type de vente :
                    <select name="type_vente" required>
                        <option value="immediate">Achat immédiat</option>
                        <option value="negociation">Négociation</option>
                        <option value="enchere">Enchère</option>
                    </select>
                </label><br><br>
                <label>Image : <input type="file" name="image" accept="image/*"></label><br><br>
                <button type="submit">Ajouter l'article</button>
            </form>

            <br><a href="votrecompte.php">⬅ Retour à votre compte</a>
      

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




   