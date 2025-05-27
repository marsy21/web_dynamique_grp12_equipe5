<?php
session_start();
include("connexion.php"); // connexion à la base de données

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: login.php");
    exit();
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupération des infos de l'utilisateur
$sql = "SELECT u.*, c.id_client, v.id_vendeur, a.id_admin
        FROM utilisateur u
        LEFT JOIN client c ON u.id_utilisateur = c.id_utilisateur
        LEFT JOIN vendeur v ON u.id_utilisateur = v.id_utilisateur
        LEFT JOIN administrateur a ON u.id_utilisateur = a.id_utilisateur
        WHERE u.id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_utilisateur);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Détermination du rôle
$role = "Client";
if ($user['id_admin']) {
    $role = "Administrateur";
} elseif ($user['id_vendeur']) {
    $role = "Vendeur";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre Compte - Agora Francia</title>
    <link rel="stylesheet" href="styles.css"> <!-- si tu as un fichier CSS -->
</head>
<body>
    <h1>Bienvenue sur votre compte, <?php echo htmlspecialchars($user['prenom']); ?> !</h1>
    <p><strong>Nom :</strong> <?php echo htmlspecialchars($user['nom']); ?></p>
    <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Rôle :</strong> <?php echo $role; ?></p>

    <hr>

    <?php if ($role === "Client"): ?>
        <h2>Vos actions client</h2>
        <ul>
            <li><a href="commandes.php">Voir vos commandes</a></li>
            <li><a href="panier.php">Accéder à votre panier</a></li>
            <li><a href="enchere_client.php">Suivre vos enchères</a></li>
            <li><a href="negociations_client.php">Gérer vos négociations</a></li>
        </ul>

    <?php elseif ($role === "Vendeur"): ?>
        <h2>Vos actions vendeur</h2>
        <ul>
            <li><a href="mes_articles.php">Gérer vos articles</a></li>
            <li><a href="enchere_vendeur.php">Suivre vos enchères</a></li>
            <li><a href="negociations_vendeur.php">Gérer vos négociations</a></li>
        </ul>

    <?php elseif ($role === "Administrateur"): ?>
        <h2>Actions administrateur</h2>
        <ul>
            <li><a href="gestion_utilisateurs.php">Gérer les utilisateurs</a></li>
            <li><a href="validation_vendeurs.php">Valider les vendeurs</a></li>
            <li><a href="moderation_articles.php">Modérer les articles</a></li>
        </ul>
    <?php endif; ?>

    <hr>
    <a href="logout.php">Se déconnecter</a>
</body>
</html>
