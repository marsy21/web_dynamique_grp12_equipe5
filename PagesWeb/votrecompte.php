<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

include 'db.php';
$id = $_SESSION['utilisateur']['id'];

// Vérif si client
$res_client = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
$is_client = mysqli_num_rows($res_client) > 0;
$client_info = $is_client ? mysqli_fetch_assoc($res_client) : null;

// Vérif si vendeur
$res_vendeur = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
$is_vendeur = mysqli_num_rows($res_vendeur) > 0;
$vendeur_info = $is_vendeur ? mysqli_fetch_assoc($res_vendeur) : null;

$utilisateur = $_SESSION['utilisateur'];
?>

<h1>Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?> <?= htmlspecialchars($utilisateur['nom']) ?></h1>
<p>Email : <?= htmlspecialchars($utilisateur['email']) ?></p>
<p>Date de création : <?= htmlspecialchars($utilisateur['date_creation']) ?></p>

<?php if ($is_client): ?>
    <h3>Informations Client</h3>
    <ul>
        <li>Adresse : <?= htmlspecialchars($client_info['adresse1'] ?? 'N/A') ?></li>
        <li>Ville : <?= htmlspecialchars($client_info['ville'] ?? 'N/A') ?></li>
    </ul>
<?php else: ?>
    <p>Souhaitez-vous acheter ? <a href="devenirclient.php">Devenir Client</a></p>
<?php endif; ?>

<?php if ($is_vendeur): ?>
    <h3>Informations Vendeur</h3>
    <ul>
        <li>Pseudo : <?= htmlspecialchars($vendeur_info['pseudo'] ?? 'N/A') ?></li>
    </ul>
<?php else: ?>
    <p>Voulez-vous vendre ? <a href="devenirvendeur.php">Devenir Vendeur</a></p>
<?php endif; ?>
