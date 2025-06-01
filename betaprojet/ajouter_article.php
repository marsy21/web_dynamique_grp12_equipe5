<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un article</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            width: 500px;
            margin: 30px auto;
            padding: 25px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        label { font-weight: bold; margin-top: 15px; display: block; }
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
        <a href="notifications.php">Notifications</a>
        <a href="panier.php">Panier</a>
        <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
        <div class="form-container">
            <h2>Ajouter un article</h2>
            <form action="traitement_ajout_article.php" method="POST" enctype="multipart/form-data">
                <label>Nom :</label>
                <input type="text" name="nom" required>

                <label>Description :</label>
                <textarea name="description" required></textarea>

                <label>Prix initial (€) :</label>
                <input type="number" name="prix_initial" step="0.01" required>

                <label>Catégorie :</label>
                <select name="categorie" required>
                    <option value="Meubles et objets d’art">Meubles et objets d’art</option>
                    <option value="Accessoire VIP">Accessoire VIP</option>
                    <option value="Matériels scolaires">Matériels scolaires</option>
                </select>

                <label>Rareté :</label>
                <select name="rarete" required>
                    <option value="Réguliers">Réguliers</option>
                    <option value="Rares">Rares</option>
                    <option value="Haut de gamme">Haut de gamme</option>
                </select>

                <label>Type de vente :</label>
                <select name="type_vente" required>
                    <option value="immediate">Achat immédiat</option>
                    <option value="negociation">Négociation</option>
                    <option value="meilleure offre">Meilleure offre (enchère)</option>
                </select>

                <div id="duree_enchere_block" style="display:none;">
                    <label>Durée de l'enchère :</label>
                    <select name="duree_enchere">
                        <option value="2">2 minutes</option>
                        <option value="10">10 minutes</option>
                        <option value="30">30 minutes</option>
                        <option value="60">1 heure</option>
                        <option value="180">3 heures</option>
                        <option value="720">12 heures</option>
                        <option value="1440">24 heures</option>
                    </select>
                </div>

                <label>Image :</label>
                <input type="file" name="image" accept="image/*" required>

                <br><br>
                <input type="submit" value="Ajouter l'article">
            </form>
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

<script>
document.querySelector('[name="type_vente"]').addEventListener('change', function() {
    const block = document.getElementById('duree_enchere_block');
    block.style.display = (this.value === 'meilleure offre') ? 'block' : 'none';
});
</script>

</body>
</html>
