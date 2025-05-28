<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

include 'db.php';
$id = $_SESSION['utilisateur']['id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = mysqli_real_escape_string($db, $_POST['pseudo'] ?? '');

    // Gestion des fichiers uploadés
    $photo_profil = null;
    $image_fond = null;

    if (!empty($_FILES['photo_profil']['name']) && $_FILES['photo_profil']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['photo_profil']['tmp_name'];
        $ext = pathinfo($_FILES['photo_profil']['name'], PATHINFO_EXTENSION);
        $photo_profil = uniqid('profil_') . '.' . $ext;
        move_uploaded_file($tmp_name, "uploads/$photo_profil");
    }

    if (!empty($_FILES['image_fond']['name']) && $_FILES['image_fond']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['image_fond']['tmp_name'];
        $ext = pathinfo($_FILES['image_fond']['name'], PATHINFO_EXTENSION);
        $image_fond = uniqid('fond_') . '.' . $ext;
        move_uploaded_file($tmp_name, "uploads/$image_fond");
    }

    // Vérifier si l'utilisateur est déjà vendeur
    $check = mysqli_query($db, "SELECT * FROM vendeurs WHERE id = $id");
    if (mysqli_num_rows($check) == 0) {
        $stmt = mysqli_prepare($db, "INSERT INTO vendeurs (id, pseudo, photo_profil, image_fond) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "isss", $id, $pseudo, $photo_profil, $image_fond);
    } else {
        // Si pas d'image uploadée, ne pas écraser les anciens champs
        if ($photo_profil === null || $image_fond === null) {
            $row = mysqli_fetch_assoc($check);
            if ($photo_profil === null) $photo_profil = $row['photo_profil'];
            if ($image_fond === null) $image_fond = $row['image_fond'];
        }

        $stmt = mysqli_prepare($db, "UPDATE vendeurs SET pseudo = ?, photo_profil = ?, image_fond = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "sssi", $pseudo, $photo_profil, $image_fond, $id);
    }

    if (mysqli_stmt_execute($stmt)) {
        $from = $_SESSION['last_page'] ?? 'votrecompte.php';
        header("Location: $from");
        exit;
    } else {
        $message = "Erreur lors de la sauvegarde des données.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Devenir Vendeur</title>
</head>
<body>
  <h1>Informations pour devenir vendeur</h1>

  <?php if ($message): ?>
    <p style="color:red;"><?= htmlspecialchars($message) ?></p>
  <?php endif; ?>

  <form method="post" action="" enctype="multipart/form-data">
    <label>Pseudo : <input type="text" name="pseudo" required></label><br><br>
    <label>Photo de profil : <input type="file" name="photo_profil" accept="image/*"></label><br><br>
    <label>Image de fond : <input type="file" name="image_fond" accept="image/*"></label><br><br>
    <button type="submit">Valider</button>
  </form>
</body>
</html>
