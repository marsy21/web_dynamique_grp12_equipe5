<?php
session_start();
if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

include 'db.php';
$id = $_SESSION['utilisateur']['id'];
$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse1 = mysqli_real_escape_string($db, $_POST['adresse1'] ?? '');
    $adresse2 = mysqli_real_escape_string($db, $_POST['adresse2'] ?? '');
    $ville = mysqli_real_escape_string($db, $_POST['ville'] ?? '');
    $code_postal = mysqli_real_escape_string($db, $_POST['code_postal'] ?? '');
    $pays = mysqli_real_escape_string($db, $_POST['pays'] ?? '');
    $telephone = mysqli_real_escape_string($db, $_POST['telephone'] ?? '');

    $check = mysqli_query($db, "SELECT * FROM clients WHERE id = $id");
    if (mysqli_num_rows($check) == 0) {
        $stmt = mysqli_prepare($db, "INSERT INTO clients (id, adresse1, adresse2, ville, code_postal, pays, telephone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "issssss", $id, $adresse1, $adresse2, $ville, $code_postal, $pays, $telephone);
    } else {
        $stmt = mysqli_prepare($db, "UPDATE clients SET adresse1 = ?, adresse2 = ?, ville = ?, code_postal = ?, pays = ?, telephone = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $adresse1, $adresse2, $ville, $code_postal, $pays, $telephone, $id);
    }

    $from = $_SESSION['last_page'] ?? 'votrecompte.php';
    header("Location: $from");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devenir Client</title>
</head>
<body>
    <h1>Informations pour devenir client</h1>
    <?php if ($erreur): ?>
        <p style="color:red;"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <form method="post">
        <label>Adresse 1 : <input type="text" name="adresse1" required></label><br><br>
        <label>Adresse 2 : <input type="text" name="adresse2"></label><br><br>
        <label>Ville : <input type="text" name="ville" required></label><br><br>
        <label>Code postal : <input type="text" name="code_postal" required></label><br><br>
        <label>Pays : <input type="text" name="pays" required></label><br><br>
        <label>Téléphone : <input type="text" name="telephone" required></label><br><br>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
