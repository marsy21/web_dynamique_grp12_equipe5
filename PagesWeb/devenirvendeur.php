<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}
$id = $_SESSION['utilisateur']['id'];

include 'db.php';

$check = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
if (mysqli_num_rows($check) == 0) {
    $sql = "INSERT INTO vendeurs (id) VALUES ($id)";
    mysqli_query($db, $sql);
}

$from = $_SERVER['HTTP_REFERER'] ?? 'votrecompte.php';
header("Location: $from");
exit;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Devenir Vendeur</title>
</head>
<body>
  <h1>Informations pour devenir vendeur</h1>
  <form method="post" action="" enctype="multipart/form-data">
    <label>Pseudo : <input type="text" name="pseudo" required></label><br>
    <label>Photo de profil : <input type="file" name="photo_profil" accept="image/*"></label><br>
    <label>Image de fond : <input type="file" name="image_fond" accept="image/*"></label><br>
    <button type="submit">Valider</button>
  </form>
</body>
</html>

