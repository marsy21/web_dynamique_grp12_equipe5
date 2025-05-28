<?php
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mdp = $_POST['mot_de_passe'];

    if (empty($nom) || empty($prenom) || empty($email) || empty($mdp)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } else {
        $mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);
        $db = mysqli_connect('localhost', 'root', '', 'agora_francia');

        if ($db) {
            $email = mysqli_real_escape_string($db, $email);
            $sql_check = "SELECT id FROM utilisateurs WHERE email = '$email'";
            $res_check = mysqli_query($db, $sql_check);

            if (mysqli_num_rows($res_check) > 0) {
                $erreur = "Cet email est déjà utilisé.";
            } else {
                $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) 
                        VALUES ('$nom', '$prenom', '$email', '$mdp_hash', 'client')";
                if (mysqli_query($db, $sql)) {
                    header("Location: connexion.php");
                    exit;
                } else {
                    $erreur = "Erreur lors de la création du compte.";
                }
            }
            mysqli_close($db);
        } else {
            $erreur = "Connexion à la base échouée.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un compte</title>
</head>
<body>
    <h1>Créer un compte</h1>
    <form method="post">
        <label>Nom : <input type="text" name="nom" required></label><br>
        <label>Prénom : <input type="text" name="prenom" required></label><br>
        <label>Email : <input type="email" name="email" required></label><br>
        <label>Mot de passe : <input type="password" name="mot_de_passe" required></label><br>
        <button type="submit">Créer le compte</button>
    </form>
    <p style="color:red"><?= $erreur ?></p>
</body>
</html>