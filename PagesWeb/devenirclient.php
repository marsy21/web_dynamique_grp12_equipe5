<?php 
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur'])) {
    header("Location: connexion.php");
    exit;
}

$idUtilisateur = $_SESSION['utilisateur']['id'];
$erreur = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Champs carte (nettoyés)
    $type_carte = $_POST['type_carte'] ?? '';
    $numero_carte = preg_replace('/\D/', '', $_POST['numero_carte'] ?? '');
    $nom_carte = trim($_POST['nom_carte'] ?? '');
    $expiration = $_POST['expiration'] ?? '';
    $code_securite = preg_replace('/\D/', '', $_POST['code_securite'] ?? '');

    $errors = [];

    // Validations
    if (!in_array($type_carte, ['Visa','MasterCard','AmericanExpress','PayPal'])) {
        $errors[] = "Type de carte invalide.";
    }
    if (strlen($numero_carte) < 12 || strlen($numero_carte) > 20) {
        $errors[] = "Numéro de carte invalide.";
    }
    if (empty($nom_carte)) {
        $errors[] = "Nom sur la carte requis.";
    }
    if (!empty($expiration)) {
        $date_exp = DateTime::createFromFormat('Y-m-d', $expiration);
        if ($date_exp) {
            $date_exp->modify('last day of this month');
            $now = new DateTime();
            if ($date_exp < $now) {
                $errors[] = "Carte expirée.";
            }
            // ✅ On formate la date au bon format pour l'enregistrement
            $exp_format = substr($expiration, 0, 7) . '-01';
        } else {
            $errors[] = "Format de date invalide.";
        }
    } else {
        $errors[] = "Date d'expiration invalide.";
    }

    if (strlen($code_securite) < 3 || strlen($code_securite) > 4) {
        $errors[] = "Code de sécurité invalide.";
    }

    if (!empty($errors)) {
        $erreur = implode('<br>', $errors);
    } else {
        // 👉 Insertion de la carte avec date formatée
        $stmt = mysqli_prepare($db, "INSERT INTO cartesreelles (type_carte, numero_carte, nom_carte, expiration, code_securite) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssss", $type_carte, $numero_carte, $nom_carte, $exp_format, $code_securite);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Adresse
        $adresse1 = mysqli_real_escape_string($db, $_POST['adresse1'] ?? '');
        $adresse2 = mysqli_real_escape_string($db, $_POST['adresse2'] ?? '');
        $ville = mysqli_real_escape_string($db, $_POST['ville'] ?? '');
        $code_postal = mysqli_real_escape_string($db, $_POST['code_postal'] ?? '');
        $pays = mysqli_real_escape_string($db, $_POST['pays'] ?? '');
        $telephone = mysqli_real_escape_string($db, $_POST['telephone'] ?? '');

        // Enregistrement / MAJ adresse
        $check = mysqli_query($db, "SELECT * FROM clients WHERE id = $idUtilisateur");
        if (mysqli_num_rows($check) == 0) {
            $stmt = mysqli_prepare($db, "INSERT INTO clients (id, adresse1, adresse2, ville, code_postal, pays, telephone) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "issssss", $idUtilisateur, $adresse1, $adresse2, $ville, $code_postal, $pays, $telephone);
        } else {
            $stmt = mysqli_prepare($db, "UPDATE clients SET adresse1 = ?, adresse2 = ?, ville = ?, code_postal = ?, pays = ?, telephone = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "ssssssi", $adresse1, $adresse2, $ville, $code_postal, $pays, $telephone, $idUtilisateur);
        }

        if (!mysqli_stmt_execute($stmt)) {
            $erreur = "Erreur lors de l'enregistrement de l'adresse.";
        } else {
            $from = $_SESSION['last_page'] ?? 'votrecompte.php';
            header("Location: $from");
            exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>connexion</title>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <link rel="stylesheet" href="style.css">
  <style>
    nav a[href="votrecompte.php"] {
    background-color: orange;
    color: white;
    }
  </style>

</head>

<body>
  <div class="wrapper">
    <header>
      <h1>Agora Francia</h1>
      <img src="Articles/Images/logo.png" alt="Logo Agora">
    </header>

    <nav>
        <a href="index.php">Accueil</a>
        <a href="toutparcourir.php">Tout Parcourir</a>
        <a href="panier.php">Panier</a>
        <a href="mesarticles.php">Mes Articles</a>
        <a href="notification.php">Notifications</a>
        <a href="votrecompte.php">Votre Compte</a>
    </nav>

    <section>
<h1>Informations pour devenir client</h1>
    <?php if ($erreur): ?>
        <p style="color:red;"><?= htmlspecialchars($erreur) ?></p>
    <?php endif; ?>
    <form method="post">
    <h2>Adresse de livraison</h2>
    <label>Adresse 1 : <input type="text" name="adresse1" required></label><br><br>
    <label>Adresse 2 : <input type="text" name="adresse2"></label><br><br>
    <label>Ville : <input type="text" name="ville" required></label><br><br>
    <label>Code postal : <input type="text" name="code_postal" required></label><br><br>
    <label>Pays : <input type="text" name="pays" required></label><br><br>
    <label>Téléphone : <input type="text" name="telephone" required></label><br><br>

    <h2>Informations de paiement</h2>
    <label>Type de carte :
        <select name="type_carte" required>
            <option value="">--Sélectionner--</option>
            <option value="Visa">Visa</option>
            <option value="MasterCard">MasterCard</option>
            <option value="AmericanExpress">American Express</option>
            <option value="PayPal">PayPal</option>
        </select>
    </label><br><br>

    <label>Numéro de carte : <input type="text" name="numero_carte" maxlength="20" required></label><br><br>
    <label>Nom sur la carte : <input type="text" name="nom_carte" maxlength="100" required></label><br><br>
    <label>Date d'expiration : <input type="date" name="expiration" required></label><br><br>
    <label>Code de sécurité : <input type="text" name="code_securite" maxlength="4" required></label><br><br>
    <label>
  <input type="checkbox" name="accepte_clause" required>
  Je reconnais qu’en faisant une offre sur un article, je suis légalement tenu(e) de l’acheter si le vendeur accepte mon offre.
</label><br><br>


    <button type="submit" name="valider_formulaire">Valider</button>
</form>


  
    <p><a href="votrecompte.php">Revenir a la page Votre Comte </a></p>
    </section>

    <footer>
      <div class="footer-content">
        <div class="footer-left">
          <p>📍 Agora Francia</p>
          <p>12 rue de Victor Hugo, 75015 Paris</p>
          <p>📞 01 23 45 67 89</p>
          <p>📧 contact@agorafrancia.fr</p>
        </div>
        <div class="footer-right">
          <img src="Articles/Images/logo.png" alt="Logo Agora">
        </div>
      </div>
    </footer>
  </div>
</body>
</html>