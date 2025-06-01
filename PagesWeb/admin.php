<?php
session_start();
include 'db.php';

// Vérification que l'utilisateur est un administrateur
if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'admin') {
    header('Location: connexion.php');
    exit;
}

// Suppression utilisateur si demande
if (isset($_GET['supprimer'])) {
    $idASupprimer = intval($_GET['supprimer']);

    // Ne pas permettre de se supprimer soi-même
    if ($idASupprimer !== $_SESSION['utilisateur']['id']) {
        $sqlSuppr = "DELETE FROM utilisateurs WHERE id = ?";
        $stmt = mysqli_prepare($db, $sqlSuppr);
        mysqli_stmt_bind_param($stmt, "i", $idASupprimer);
        mysqli_stmt_execute($stmt);
    }
}

// Récupération de tous les utilisateurs
$sql = "SELECT id, nom, prenom, email, role, date_creation FROM utilisateurs ORDER BY date_creation DESC";
$result = mysqli_query($db, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Comptes Utilisateur</title>
    <link rel="stylesheet" href="style.css">
    <style>
        nav a[href="admin.php"] {
            background-color: orange;
            color: white;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: center; }
        .prix { color: purple; font-weight: bold; }
        .total { text-align: right; font-size: 1.3em; margin-top: 15px; }
        a.supprimer { color: red; text-decoration: none; font-weight: bold; }
        a.supprimer:hover { text-decoration: underline; }
        .btn-payer {
            margin-top: 20px;
            background-color: brown;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }
        .btn-payer:hover { background-color: #a0522d; }
    </style>
</head>
<body>
    <div class="wrapper">
        <header>
            <h1>Agora Francia - Administration</h1>
        </header>

        <nav>
            <a href="index.php">Accueil</a>
            <a href="toutparcourir.php">Tout Parcourir</a>
            <a href="panier.php">Panier</a>
            <a href="mesarticles.php">Mes Articles</a>
            <a href="notification.php">Notifications</a>
            <a href="votrecompte.php">Votre Compte</a>
            <a href="admin.php">Les pleins pouvoirs</a>
        </nav>

        <section>
            <h2>Gestion des comptes utilisateurs</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Date création</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['nom']) ?></td>
                            <td><?= htmlspecialchars($user['prenom']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= htmlspecialchars($user['date_creation']) ?></td>
                            <td>
                                <?php if ($user['id'] !== $_SESSION['utilisateur']['id']) : ?>
                                    <a class="supprimer" href="admin.php?supprimer=<?= $user['id'] ?>" onclick="return confirm('Confirmer la suppression de cet utilisateur ?');">Supprimer</a>
                                <?php else : ?>
                                    <em>Vous</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <footer>
            <p>© Agora Francia - Administration</p>
        </footer>
    </div>
</body>
</html>
