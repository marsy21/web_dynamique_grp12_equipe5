<?php
session_start();
include 'db.php';

$connect_msg = "Nous sommes le meilleur site de ventes vintage de toute la France. Vous pouvez vendre, acheter ou même devenir un de nos fournisseurs. Inscrivez-vous vite !!!";

$is_client = false;
$is_vendeur = false;
$pseudo_vendeur = null;

if (isset($_SESSION['utilisateur'])) {
    $id = $_SESSION['utilisateur']['id'];
    $utilisateur = $_SESSION['utilisateur'];

    $res_client = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
    $is_client = mysqli_num_rows($res_client) > 0;

    $res_vendeur = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
    $is_vendeur = mysqli_num_rows($res_vendeur) > 0;
    $pseudo_vendeur = $is_vendeur ? mysqli_fetch_assoc($res_vendeur)['pseudo'] : null;

    $connect_msg = "Connecté";
    if ($is_client && $is_vendeur) {
        $connect_msg = "Connecté Client et Vendeur $pseudo_vendeur";
    } elseif ($is_client) {
        $connect_msg = "Connecté Client";
    } elseif ($is_vendeur) {
        $connect_msg = "Connecté $pseudo_vendeur";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Agora Francia</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="style.css">

    <style>
        nav a[href="toutparcourir.php"] {
            background-color: orange;
            color: white;
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
            display: inline-block;
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
        <p><?= htmlspecialchars($connect_msg) ?></p>
        <div style="text-align:center; margin: 20px;">
            <label for="rarete">Rareté :</label>
            <select id="rarete" onchange="filtrerArticles()">
                <option value="">Toutes</option>
                <option value="Rares">Rares</option>
                <option value="Haut de gamme">Haut de gamme</option>
                <option value="Réguliers">Réguliers</option>
            </select>
            <br>

            <label for="categorie">Catégorie :</label>
            <select id="categorie" onchange="filtrerArticles()">
                <option value="">Toutes</option>
                <option value="Meubles et objets d’art">Meubles et objets d’art</option>
                <option value="Accessoire VIP">Accessoire VIP</option>
                <option value="Matériels scolaires">Matériels scolaires</option>
            </select>
            <br>

            <label for="type_vente">Type de vente :</label>
            <select id="type_vente" onchange="filtrerArticles()">
                <option value="">Tous</option>
                <option value="meilleure offre">Meilleur offre</option>
                <option value="negociation">Négociation</option>
                <option value="immediate">Achat immédiat</option>
            </select>
        </div>
    </section>

    <div class="articles-container">
        <?php
        $sql = "
            SELECT a.id, a.nom, a.prix_initial, a.categorie, a.rarete, a.type_vente, p.url
            FROM articles a
            LEFT JOIN photos p ON a.id = p.article_id
            WHERE a.vendu = 0
            GROUP BY a.id
        ";

        $result = mysqli_query($db, $sql);

        while ($data = mysqli_fetch_assoc($result)) {
            $id = $data['id'];
            $nom = $data['nom'];
            $prix = number_format($data['prix_initial'], 2, ',', '') . "€";
            $img_path = "Articles/Images/" . htmlspecialchars($data['url']);
            $categorie = $data['categorie'];
            $rarete = $data['rarete'];
            $type_vente = $data['type_vente'];

            echo "<a href='monarticle.php?id=$id' class='article-card' 
                  data-rarete='" . htmlspecialchars($rarete, ENT_QUOTES) . "' 
                  data-categorie='" . htmlspecialchars($categorie, ENT_QUOTES) . "' 
                  data-type='" . htmlspecialchars($type_vente, ENT_QUOTES) . "'>";
            echo "<img src='$img_path' alt='" . htmlspecialchars($nom) . "' />";
            echo "<div class='nom'>" . htmlspecialchars($nom) . "</div>";
            echo "<div class='prix'>$prix</div>";
            echo "</a>";
        }

        mysqli_close($db);
        ?>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-left">
                <p>📍 Agora Francia</p>
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

<script>
function filtrerArticles() {
    const rarete = document.getElementById('rarete').value;
    const categorie = document.getElementById('categorie').value;
    const typeVente = document.getElementById('type_vente').value;

    const cards = document.querySelectorAll('.article-card');

    cards.forEach(card => {
        const r = card.getAttribute('data-rarete');
        const c = card.getAttribute('data-categorie');
        const t = card.getAttribute('data-type');

        const matchRarete = (rarete === "") || (r === rarete);
        const matchCategorie = (categorie === "") || (c === categorie);
        const matchType = (typeVente === "") || (t === typeVente);

        card.style.display = (matchRarete && matchCategorie && matchType) ? 'inline-block' : 'none';
    });
}
</script>

</body>
</html>
