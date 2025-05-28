
<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

include 'db.php';
$id = $_SESSION['utilisateur']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = mysqli_real_escape_string($db, $_POST['pseudo']);
    $photo_profil = $_FILES['photo_profil']['name'] ?? null;
    $image_fond = $_FILES['image_fond']['name'] ?? null;

    // upload des images (exemple simplifié)
    if ($photo_profil) {
        move_uploaded_file($_FILES['photo_profil']['tmp_name'], "uploads/$photo_profil");
    }
    if ($image_fond) {
        move_uploaded_file($_FILES['image_fond']['tmp_name'], "uploads/$image_fond");
    }

    // vérifier si l'utilisateur est déjà vendeur
    $check = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
    if (mysqli_num_rows($check) == 0) {
        $sql = "INSERT INTO vendeurs (id, pseudo, photo_profil, image_fond) 
                VALUES ($id, '$pseudo', '$photo_profil', '$image_fond')";
        mysqli_query($db, $sql);
    } else {
        $sql = "UPDATE vendeurs SET pseudo='$pseudo', photo_profil='$photo_profil', image_fond='$image_fond' 
                WHERE id=$id";
        mysqli_query($db, $sql);
    }

    $from = $_SESSION['last_page'] ?? 'votrecompte.php';
    header("Location: $from");
    exit;
}
?>
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

