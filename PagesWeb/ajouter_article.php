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
    $prix_initial = floatval($_POST['prix_initial'] ?? 0);
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
    $query = "INSERT INTO articles (nom, description, prix_initial, type_vente, id_vendeur) 
              VALUES ('$nom', '$description', $prix_initial, '$type_vente', $id_utilisateur)";
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
    <meta charset="UTF-8">
    <title>Ajouter un article</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Ajouter un article</h1>
        </header>

        <nav>
            <a href="index.php">Accueil</a>
            <a href="toutparcourir.php">Tout Parcourir</a>
            <a href="votrecompte.php">Votre Compte</a>
        </nav>

        <section>
            <?php if ($message): ?>
                <p style="color:green;"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <label>Nom : <input type="text" name="nom" required></label><br><br>
                <label>Description :<br><textarea name="description" rows="5" cols="40" required></textarea></label><br><br>
                <label>Prix initial (€) : <input type="number" name="prix_initial" step="0.01" required></label><br><br>
                <label>Type de vente :
                    <select name="type_vente" required>
                        <option value="immediate">Achat immédiat</option>
                        <option value="negociation">Négociation</option>
                        <option value="meilleure offre">Meilleure offre</option>
                    </select>
                </label><br><br>
                <label>Image : <input type="file" name="image" accept="image/*"></label><br><br>
                <button type="submit">Ajouter l'article</button>
            </form>

            <br><a href="votrecompte.php">⬅ Retour à votre compte</a>
        </section>
    </div>
</body>
</html>
