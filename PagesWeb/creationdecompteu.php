<?php
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mdp = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);

    $db = mysqli_connect('localhost', 'root', '', 'agora_francia');

    if ($db) {
        $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) 
                VALUES ('$nom', '$prenom', '$email', '$mdp', 'client')";
        if (mysqli_query($db, $sql)) {
            header("Location: connexion.php");
            exit;
        } else {
            $erreur = "Email déjà utilisé ou erreur dans l'insertion.";
        }
        mysqli_close($db);
    } else {
        $erreur = "Connexion BDD échouée.";
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
