<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$id_vendeur = intval($_SESSION['utilisateur']['id']);

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $prix = floatval($_POST['prix'] ?? 0);
    $categorie = $_POST['categorie'] ?? null;
    $rarete = $_POST['rarete'] ?? null;
    $type_vente = $_POST['type_vente'] ?? 'immediate';
    $qualite = trim($_POST['qualite'] ?? '');
    $defaut = trim($_POST['defaut'] ?? '');

    // Validation simple
    if (!$nom || !$description || $prix <= 0 || !$categorie || !$rarete || !$type_vente) {
        $message = "Merci de remplir tous les champs obligatoires correctement.";
    } else {
        // Gestion de l'upload de l'image
        $image_url = null;
        if (!empty($_FILES['image']['name'])) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowed_types)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $ext;
                $destination = 'Articles/Images/' . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $image_url = $filename;
                } else {
                    $message = "Erreur lors de l'upload de l'image.";
                }
            } else {
                $message = "Format d'image non supporté. Formats acceptés : jpeg, png, gif.";
            }
        }

        if (!$message) {
            // Insertion dans la table articles
            $stmt = mysqli_prepare($db, "INSERT INTO articles 
                (id_vendeur, nom, description, prix_initial, categorie, rarete, type_vente, qualite, defaut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issdsssss",
                $id_vendeur, $nom, $description, $prix, $categorie, $rarete, $type_vente, $qualite, $defaut);
            mysqli_stmt_execute($stmt);
            $id_article = mysqli_stmt_insert_id($stmt);
            mysqli_stmt_close($stmt);

            // Insertion photo si image uploadée
            if ($image_url) {
                $stmt2 = mysqli_prepare($db, "INSERT INTO photos (article_id, url) VALUES (?, ?)");
                mysqli_stmt_bind_param($stmt2, "is", $id_article, $image_url);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);
            }

            $message = "Article ajouté avec succès !";
        }
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
        nav a[href="mesarticles.php"] { background-color: orange; color: white; }
        label { display: block; margin: 10px 0 5px; }
        input[type="text"], input[type="number"], textarea, select {
            width: 300px; padding: 5px;
        }
        button {
            margin-top: 15px;
            background-color: brown;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
        }
        .message {
            color: green;
            margin-bottom: 15px;
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
        <h1>Ajouter un Article</h1>

        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <label>Nom :</label>
            <input type="text" name="nom" required>

            <label>Description :</label>
            <textarea name="description" rows="5" required></textarea>

            <label>Prix (€) :</label>
            <input type="number" name="prix" step="0.01" required min="0.01">

            <label>Catégorie :</label>
            <select name="categorie" required>
                <option value="">-- Choisir --</option>
                <option value="Meubles et objets d’art">Meubles et objets d’art</option>
                <option value="Accessoire VIP">Accessoire VIP</option>
                <option value="Materiels scolaires">Materiels scolaires</option>
            </select>

            <label>Rareté :</label>
            <select name="rarete" required>
                <option value="">-- Choisir --</option>
                <option value="Rares">Rares</option>
                <option value="Haut de gamme">Haut de gamme</option>
                <option value="Réguliers">Réguliers</option>
            </select>

            <label>Type de vente :</label>
            <select name="type_vente" required>
                <option value="immediate">Achat immédiat</option>
                <option value="negociation">Négociation</option>
                <option value="meilleure offre">Meilleure offre</option>
            </select>

            <label>Qualité :</label>
            <input type="text" name="qualite">

            <label>Défaut :</label>
            <input type="text" name="defaut">

            <label>Image :</label>
            <input type="file" name="image" accept="image/*">

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
