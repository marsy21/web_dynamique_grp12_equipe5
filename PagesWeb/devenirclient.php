<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}
$id = $_SESSION['utilisateur']['id'];

include 'db.php'; // ton fichier de connexion

// Prévenir les doublons
$check = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
if (mysqli_num_rows($check) == 0) {
    $sql = "INSERT INTO clients (id) VALUES ($id)";
    mysqli_query($db, $sql);
}

// Retour à la page précédente
$from = $_SERVER['HTTP_REFERER'] ?? 'votrecompte.php';
header("Location: $from");
exit;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Devenir Client</title>
</head>
<body>
  <h1>Informations pour devenir client</h1>
  <form method="post" action="">
    <label>Adresse 1 : <input type="text" name="adresse1" required></label><br>
    <label>Adresse 2 : <input type="text" name="adresse2"></label><br>
    <label>Ville : <input type="text" name="ville" required></label><br>
    <label>Code postal : <input type="text" name="code_postal" required></label><br>
    <label>Pays : <input type="text" name="pays" required></label><br>
    <label>Téléphone : <input type="text" name="telephone" required></label><br>
    <button type="submit">Valider</button>
  </form>
</body>
</html>
