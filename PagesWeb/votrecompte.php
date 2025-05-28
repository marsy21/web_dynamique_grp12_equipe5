<?php
session_start();

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Votre compte</title>
</head>
<body>
    <h1>Bienvenue <?= htmlspecialchars($utilisateur['prenom']) ?> <?= htmlspecialchars($utilisateur['nom']) ?></h1>
    <p>Email : <?= htmlspecialchars($utilisateur['email']) ?></p>
    <p>Rôle : <?= htmlspecialchars($utilisateur['role']) ?></p>
    <p>Date de création : <?= htmlspecialchars($utilisateur['date_creation']) ?></p>

    <form method="post" action="logout.php">
        <button type="submit">Se déconnecter</button>
    </form>
</body>
</html>
