<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header('Location: connexion.php');
    exit;
}

$userId = intval($_SESSION['utilisateur']['id']);

$categories = ['Meubles et objets d’art', 'Accessoire VIP', 'Matériels scolaires'];
$message = '';

$type_vente_options = [
    'immediate' => 'Achat immédiat',
    'negociation' => 'Négociation',
    'meilleuroffre' => 'Meilleure offre',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter'])) {
    $mot_cle = trim($_POST['mot_cle']);
    $categorie = in_array($_POST['categorie'], $categories) ? $_POST['categorie'] : null;
    $prix_max = ($_POST['prix_max'] !== '' && is_numeric($_POST['prix_max'])) ? floatval($_POST['prix_max']) : null;
    $type_vente = array_key_exists($_POST['type_vente'], $type_vente_options) ? $_POST['type_vente'] : null;

    if (empty($mot_cle)) {
        $message = "Le mot-clé est obligatoire.";
    } elseif ($type_vente === null) {
        $message = "Le type de vente est invalide.";
    } else {
        $query = "INSERT INTO notifications (utilisateur_id, mot_cle, categorie, prix_max, type_vente, actif) VALUES (?, ?, ";
        $query .= ($categorie !== null ? "?, " : "NULL, ");
        $query .= ($prix_max !== null ? "?, " : "NULL, ");
        $query .= "?, 1)";
        $stmt = mysqli_prepare($db, $query);

        if ($categorie !== null && $prix_max !== null) {
            mysqli_stmt_bind_param($stmt, "issds", $userId, $mot_cle, $categorie, $prix_max, $type_vente);
        } elseif ($categorie !== null && $prix_max === null) {
            mysqli_stmt_bind_param($stmt, "isss", $userId, $mot_cle, $categorie, $type_vente);
        } elseif ($categorie === null && $prix_max !== null) {
            mysqli_stmt_bind_param($stmt, "isds", $userId, $mot_cle, $prix_max, $type_vente);
        } else {
            mysqli_stmt_bind_param($stmt, "iss", $userId, $mot_cle, $type_vente);
        }

        if (mysqli_stmt_execute($stmt)) {
            $message = "✅ Notification ajoutée avec succès.";
        } else {
            $message = "❌ Erreur : " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    }
}

// Gérer activation/suppression
if (isset($_GET['action'], $_GET['id'])) {
    $notifId = intval($_GET['id']);

    $stmt = mysqli_prepare($db, "SELECT id FROM notifications WHERE id = ? AND utilisateur_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $notifId, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) === 1) {
        mysqli_stmt_close($stmt);

        if ($_GET['action'] === 'supprimer') {
            $stmtDel = mysqli_prepare($db, "DELETE FROM notifications WHERE id = ?");
            mysqli_stmt_bind_param($stmtDel, "i", $notifId);
            mysqli_stmt_execute($stmtDel);
            mysqli_stmt_close($stmtDel);
            $message = "Notification supprimée.";
        } elseif ($_GET['action'] === 'toggle') {
            $stmt2 = mysqli_prepare($db, "SELECT actif FROM notifications WHERE id = ?");
            mysqli_stmt_bind_param($stmt2, "i", $notifId);
            mysqli_stmt_execute($stmt2);
            $res2 = mysqli_stmt_get_result($stmt2);
            $notif = mysqli_fetch_assoc($res2);
            mysqli_stmt_close($stmt2);

            $nouvelEtat = $notif['actif'] ? 0 : 1;
            $stmtUpd = mysqli_prepare($db, "UPDATE notifications SET actif = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmtUpd, "ii", $nouvelEtat, $notifId);
            mysqli_stmt_execute($stmtUpd);
            mysqli_stmt_close($stmtUpd);
            $message = $nouvelEtat ? "Notification activée." : "Notification désactivée.";
        }
    } else {
        mysqli_stmt_close($stmt);
        $message = "Notification introuvable.";
    }
}

// Récupération des notifications
$stmt = mysqli_prepare($db, "SELECT id, mot_cle, categorie, prix_max, type_vente, actif FROM notifications WHERE utilisateur_id = ? ORDER BY id DESC");
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$notifications = [];
while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}
mysqli_stmt_close($stmt);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Mes Notifications d'Alerte</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        .btn { padding: 6px 12px; background-color: orange; border: none; color: white; cursor: pointer; text-decoration: none; }
        .btn-danger { background-color: red; }
        .btn-toggle { background-color: #555; }
        .message { margin: 15px 0; font-weight: bold; color: green; }
        .error { color: red; }
        form { margin-top: 20px; max-width: 600px; }
        label { display: block; margin-top: 10px; }
        input, select { width: 100%; padding: 8px; font-size: 1em; margin-top: 5px; }
    </style>
</head>
<body>
<div class="wrapper">
    <header>
        <h1>Agora Francia</h1>
        <img src="Articles/Images/logo.png" alt="Logo Agora" style="height: 50px;">
    </header>

    <nav>
        <a href="index.php">Accueil</a>
        <a href="toutparcourir.php">Tout Parcourir</a>
        <a href="panier.php">Panier</a>
        <a href="votrecompte.php">Votre Compte</a>
        <a href="mesarticles.php">Mes Articles</a>
        <a href="notification.php" style="background-color: orange; color: white;">Notifications</a>
    </nav>

    <section>
        <h2>Mes alertes personnalisées</h2>

        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post">
            <h3>Nouvelle alerte</h3>
            <label for="mot_cle">Mot-clé <span style="color:red">*</span></label>
            <input type="text" name="mot_cle" id="mot_cle" required>

            <label for="categorie">Catégorie</label>
            <select name="categorie" id="categorie">
                <option value="">-- Toutes --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="prix_max">Prix max (€)</label>
            <input type="number" name="prix_max" step="0.01" min="0" id="prix_max" placeholder="Optionnel">

            <label for="type_vente">Type de vente <span style="color:red">*</span></label>
            <select name="type_vente" id="type_vente" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($type_vente_options as $val => $label): ?>
                    <option value="<?= $val ?>"><?= $label ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="ajouter" class="btn">Ajouter</button>
        </form>

        <h3>Alertes en cours</h3>
        <?php if (empty($notifications)): ?>
            <p>Aucune alerte définie.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Mot-clé</th>
                        <th>Catégorie</th>
                        <th>Prix max</th>
                        <th>Type de vente</th>
                        <th>Active</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($notifications as $notif): ?>
                    <tr>
                        <td>
                            <?= htmlspecialchars($notif['mot_cle']) ?><br>
                            <?php
// Requête préparée pour récupérer les articles correspondants
$sql = "SELECT id FROM articles WHERE nom LIKE ? AND type_vente = ?";
$params = ["%" . $notif['mot_cle'] . "%", $notif['type_vente']];
$types = "ss";

if (!empty($notif['categorie'])) {
    $sql .= " AND categorie = ?";
    $params[] = $notif['categorie'];
    $types .= "s";
}

if (!empty($notif['prix_max'])) {
    $sql .= " AND prix_initial <= ?";
    $params[] = $notif['prix_max'];
    $types .= "d";
}

$stmtCheck = mysqli_prepare($db, $sql);
mysqli_stmt_bind_param($stmtCheck, $types, ...$params);
mysqli_stmt_execute($stmtCheck);
$resultCheck = mysqli_stmt_get_result($stmtCheck);

$articles = [];
while ($row = mysqli_fetch_assoc($resultCheck)) {
    $articles[] = $row['id'];
}

$nbArticles = count($articles);

if ($nbArticles === 1): ?>
    <a href="monarticle.php?id=<?= $articles[0] ?>" style="font-size: 0.9em; color: green;">🔎 Voir l’article</a>
<?php elseif ($nbArticles > 1): ?>
    <a href="monarticle.php?id=<?= $articles[0] ?>" style="font-size: 0.9em; color: green;">🔎 Voir un article</a><br>
    <a href="toutparcourir.php?search=<?= urlencode($notif['mot_cle']) ?>" style="font-size: 0.9em; color: green;">🔍 Voir tous les articles</a>
<?php endif;

mysqli_stmt_close($stmtCheck);
?>

                        </td>
                        <td><?= $notif['categorie'] ?? 'Toutes' ?></td>
                        <td><?= $notif['prix_max'] !== null ? number_format($notif['prix_max'], 2, ',', ' ') : '—' ?></td>
                        <td><?= $type_vente_options[$notif['type_vente']] ?? $notif['type_vente'] ?></td>
                        <td><?= $notif['actif'] ? 'Oui' : 'Non' ?></td>
                        <td>
                            <a href="?action=toggle&id=<?= $notif['id'] ?>" class="btn btn-toggle"><?= $notif['actif'] ? 'Désactiver' : 'Activer' ?></a>
                            <a href="?action=supprimer&id=<?= $notif['id'] ?>" class="btn btn-danger" onclick="return confirm('Confirmer la suppression ?')">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>

    <footer>
        <div class="footer-content" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <p>📍 Agora Francia</p>
                <p>12 rue de Victor Hugo, 75015 Paris</p>
                <p>📞 01 23 45 67 89</p>
                <p>📧 contact@agorafrancia.fr</p>
            </div>
            <div>
                <img src="Articles/Images/logo.png" alt="Logo Agora" style="height: 40px;">
            </div>
        </div>
    </footer>
</div>
</body>
</html>
