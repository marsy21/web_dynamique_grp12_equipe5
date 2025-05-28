<?php
session_start();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    if (empty($email) || empty($mdp)) {
        $erreur = "Tous les champs doivent être remplis.";
    } else {
        $db = mysqli_connect('localhost', 'root', '', 'agora_francia');

        if ($db) {
            $email = mysqli_real_escape_string($db, $email);
            $query = "SELECT * FROM utilisateurs WHERE email = '$email'";
            $result = mysqli_query($db, $query);

            if ($user = mysqli_fetch_assoc($result)) {
                if (password_verify($mdp, $user['mot_de_passe'])) {
                    $_SESSION['utilisateur'] = $user;
                    header("Location: votrecompte.php");
                    exit;
                } else {
                    $erreur = "Mot de passe incorrect.";
                }
            } else {
                $erreur = "Aucun utilisateur trouvé avec cet email.";
            }

            mysqli_close($db);
        } else {
            $erreur = "Erreur de connexion à la base de données.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <form method="post">
        <label>Email : <input type="email" name="email" required></label><br>
        <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
        <button type="submit">Se connecter</button>
    </form>
    <p style="color:red"><?= $erreur ?></p>
    <p>Pas encore de compte ? <a href="creationdecompteu.php">Créer un compte</a></p>
</body>
</html>
