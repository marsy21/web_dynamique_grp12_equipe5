<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$idUtilisateur = intval($_SESSION['utilisateur']['id']);

// Vérifier que l'utilisateur est bien client
$stmt = mysqli_prepare($db, "SELECT id FROM clients WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $idUtilisateur);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $estClient);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

if (!$estClient) {
    header('Location: votrecompte.php?redirect=panier');
    exit;
}

$errors = [];
$success = false;

// Récupérer les articles dont l'enchère est terminée et remportée par cet utilisateur
// Récupérer les articles dont l'enchère est terminée et remportée par cet utilisateur
$sql = "
    SELECT e.article_id, e.prix_max 
    FROM encheres e
    WHERE e.client_id = ? AND e.terminee = 1
";
$stmt = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmt, "i", $idUtilisateur);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$articlesAPayer = [];
$total = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $articlesAPayer[] = $row['article_id'];
    $total += $row['prix_max'];
}
mysqli_stmt_close($stmt);


if (count($articlesAPayer) === 0) {
    // Pas d'enchères à payer
    header('Location: panier.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type_carte = $_POST['type_carte'] ?? '';
    $numero_carte = preg_replace('/\D/', '', $_POST['numero_carte'] ?? '');
    $nom_carte = trim($_POST['nom_carte'] ?? '');
    $expiration = $_POST['expiration'] ?? '';
    $code_securite = preg_replace('/\D/', '', $_POST['code_securite'] ?? '');

    // Validation des données
    if (!in_array($type_carte, ['Visa', 'MasterCard', 'AmericanExpress', 'PayPal'])) {
        $errors[] = "Type de carte invalide.";
    }
    if (strlen($numero_carte) < 12 || strlen($numero_carte) > 20) {
        $errors[] = "Numéro de carte invalide.";
    }
    if (empty($nom_carte)) {
        $errors[] = "Nom sur la carte est requis.";
    }
    if (!empty($expiration)) {
        $date_exp = DateTime::createFromFormat('Y-m-d', $expiration);
        if (!$date_exp) {
            $errors[] = "Date d'expiration invalide.";
        } else {
            $date_exp->modify('last day of this month');
            $now = new DateTime();
            if ($date_exp < $now) {
                $errors[] = "Carte expirée.";
            }
        }
    } else {
        $errors[] = "Date d'expiration requise.";
    }
    if (strlen($code_securite) < 3 || strlen($code_securite) > 4) {
        $errors[] = "Code de sécurité invalide.";
    }

    if (empty($errors)) {
        $stmt = mysqli_prepare($db, "SELECT id FROM cartesreelles WHERE type_carte = ? AND numero_carte = ? AND nom_carte = ? AND expiration = ? AND code_securite = ?");
        $exp_format = substr($expiration, 0, 7) . '-01'; // format YYYY-MM-01
        mysqli_stmt_bind_param($stmt, "sssss", $type_carte, $numero_carte, $nom_carte, $exp_format, $code_securite);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) === 1) {
            mysqli_stmt_close($stmt);

            $ids = implode(',', array_map('intval', $articlesAPayer));

            // Mise à jour des articles comme vendus
            $update_sql = "UPDATE articles SET vendu = 1 WHERE id IN ($ids)";
            mysqli_query($db, $update_sql);

            // Suppression des enchères payées
            $delete_sql = "DELETE FROM encheres WHERE client_id = ? AND article_id IN ($ids)";
            $stmt_del = mysqli_prepare($db, $delete_sql);
            mysqli_stmt_bind_param($stmt_del, "i", $idUtilisateur);
            mysqli_stmt_execute($stmt_del);
            mysqli_stmt_close($stmt_del);

            // Insertion dans commandes avec type_vente = 'enchere'
            $insert_sql = "INSERT INTO commandes (client_id, article_id, prix_final, date_commande, type_vente) VALUES (?, ?, ?, NOW(), 'enchere')";
            $stmt_insert = mysqli_prepare($db, $insert_sql);

            // On ré-extrait les prix max pour insérer les commandes
            foreach ($articlesAPayer as $article_id) {
                // Récupérer le prix max pour cet article
                $prix_final = 0;
                // On peut faire une requête rapide pour chaque, ou stocker avant, ici on utilise la valeur stockée précédemment
                foreach ($result as $row) {
                    if ($row['article_id'] == $article_id) {
                        $prix_final = $row['prix_max'];
                        break;
                    }
                }
                mysqli_stmt_bind_param($stmt_insert, "iid", $idUtilisateur, $article_id, $prix_final);
                mysqli_stmt_execute($stmt_insert);
            }
            mysqli_stmt_close($stmt_insert);

            $success = true;
            header('Location: votrecompte.php?paiement=success');
            exit;
        } else {
            $errors[] = "Informations de carte invalides.";
            mysqli_stmt_close($stmt);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Paiement Enchères - Agora Francia</title>
    <link rel="stylesheet" href="style.css">
    <style>
        nav a[href="panier.php"] { background-color: orange; color: white; }

        form {
            max-width: 400px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type=text], input[type=month], input[type=password], input[type=date] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            margin-top: 20px;
            background-color: brown;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #a0522d;
        }
        .errors {
            background-color: #fdd;
            border: 1px solid #f99;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 6px;
            color: #900;
        }
        .total {
            text-align: center;
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 20px;
            color: purple;
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
          <h1>Paiement Enchères</h1>

<?php if ($errors): ?>
    <div class="error">
        <ul>
            <?php foreach ($errors as $err): ?>
                <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<p>Total à payer : <span class="total"><?= number_format($total, 2, ',', '') ?> €</span></p>

<form method="post" action="paiement_enchere.php">
    <h2>Informations de paiement</h2>
    <label>Type de carte :
        <select name="type_carte" required>
            <option value="">--Sélectionner--</option>
            <option value="Visa">Visa</option>
            <option value="MasterCard">MasterCard</option>
            <option value="AmericanExpress">American Express</option>
            <option value="PayPal">PayPal</option>
        </select>
    </label><br><br>

    <label>Numéro de carte : <input type="text" name="numero_carte" maxlength="20" required></label><br><br>
    <label>Nom sur la carte : <input type="text" name="nom_carte" maxlength="100" required></label><br><br>
    <label>Date d'expiration : <input type="date" name="expiration" required></label><br><br>
    <label>Code de sécurité : <input type="text" name="code_securite" maxlength="4" required></label><br><br>

    <button type="submit" class="btn-pay">Payer <?= number_format($total, 2, ',', '') ?> €</button>
</form>

<p><a href="panier.php">← Retour au panier</a></p>

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
