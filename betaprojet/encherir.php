<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

$article_id = intval($_GET['id'] ?? 0);
$client_id = $_SESSION['utilisateur']['id'];

// Vérification que l'article existe et est une enchère
$req = $db->prepare("SELECT * FROM articles WHERE id = ? AND type_vente = 'meilleure offre'");
$req->bind_param("i", $article_id);
$req->execute();
$res = $req->get_result();

if ($res->num_rows === 0) {
    echo "Article introuvable ou non disponible pour enchère.";
    exit;
}

$article = $res->fetch_assoc();

// Traitement enchère
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = floatval($_POST['montant']);

    // Vérifier si offre déjà existante
    $check = $db->prepare("SELECT * FROM encheres WHERE article_id = ? AND client_id = ?");
    $check->bind_param("ii", $article_id, $client_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        // Mise à jour
        $upd = $db->prepare("UPDATE encheres SET montant = ?, date_enchere = NOW() WHERE article_id = ? AND client_id = ?");
        $upd->bind_param("dii", $montant, $article_id, $client_id);
        $upd->execute();
        $message = "Votre enchère a été mise à jour.";
    } else {
        // Nouvelle enchère
        $ins = $db->prepare("INSERT INTO encheres (article_id, client_id, montant) VALUES (?, ?, ?)");
        $ins->bind_param("iid", $article_id, $client_id, $montant);
        $ins->execute();
        $message = "Votre enchère a été enregistrée.";
    }
}

// Récupérer les offres existantes
$encheres = [];
$req_ench = $db->query("SELECT e.montant, c.nom, e.date_enchere
                        FROM encheres e
                        JOIN clients c ON e.client_id = c.id
                        WHERE e.article_id = $article_id
                        ORDER BY e.montant DESC LIMIT 5");

while ($row = $req_ench->fetch_assoc()) {
    $encheres[] = $row;
}

// Calcul du chrono
$fin = strtotime($article['date_fin_enchere']);
$restant = $fin - time();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Enchérir - <?= htmlspecialchars($article['nom']) ?></title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-block {
            width: 50%; margin: auto; padding: 20px; background: #f4f4f4; border-radius: 10px;
        }
        .chrono { font-size: 20px; color: darkred; font-weight: bold; }
        .offres { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <header><h1>Agora Francia</h1></header>
        <nav>
            <a href="index.php">Accueil</a>
            <a href="toutparcourir.php">Tout Parcourir</a>
            <a href="notifications.php">Notifications</a>
            <a href="panier.php">Panier</a>
            <a href="votrecompte.php">Votre Compte</a>
        </nav>

        <section>
            <div class="form-block">
                <h2>Enchérir sur : <?= htmlspecialchars($article['nom']) ?></h2>
                <p><strong>Prix de départ :</strong> <?= number_format($article['prix_initial'], 2, ',', '') ?> €</p>
                <p class="chrono">Temps restant : <span id="timer"></span></p>

                <?php if ($restant <= 0): ?>
                    <p style="color:red;">⛔ Enchère terminée.</p>
                <?php else: ?>
                    <form method="post">
                        <label>Votre offre (€) :</label>
                        <input type="number" step="0.01" name="montant" required>
                        <button type="submit">Soumettre l'enchère</button>
                    </form>
                <?php endif; ?>

                <?php if ($message): ?>
                    <p style="color:green;"><?= $message ?></p>
                <?php endif; ?>

                <div class="offres">
                    <h3>Dernières enchères :</h3>
                    <ul>
                        <?php foreach ($encheres as $e): ?>
                            <li>
                                <?= htmlspecialchars($e['nom']) ?> : 
                                <strong><?= number_format($e['montant'], 2, ',', '') ?> €</strong>
                                (<?= date('H:i:s', strtotime($e['date_enchere'])) ?>)
                            </li>
                        <?php endforeach; ?>
                        <?php if (count($encheres) === 0): ?>
                            <li>Aucune offre pour le moment.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </section>

        <footer>
            <div class="footer-content">
                <p>© Agora Francia - Enchère</p>
            </div>
        </footer>
    </div>

<script>
// Chronomètre
let tempsRestant = <?= max(0, $restant) ?>;
function updateTimer() {
    if (tempsRestant <= 0) {
        document.getElementById("timer").innerText = "Terminé";
        return;
    }
    const m = Math.floor(tempsRestant / 60);
    const s = tempsRestant % 60;
    document.getElementById("timer").innerText = m + "m " + s + "s";
    tempsRestant--;
}
setInterval(updateTimer, 1000);
updateTimer();
</script>

</body>
</html>
