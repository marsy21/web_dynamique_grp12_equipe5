<?php
session_start();
include("connexion.php"); // $conn = new mysqli(...);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $role = $_POST['role']; // "client" ou "vendeur"

    // Vérifier email non utilisé
    $sql = "SELECT id FROM utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Cet email est déjà utilisé.";
    } else {
        // Insérer dans utilisateurs
        $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nom, $prenom, $email, $mot_de_passe, $role);
        $stmt->execute();

        $new_id = $stmt->insert_id;

        // Insérer dans table spécifique selon role
        if ($role === "client") {
            $sql2 = "INSERT INTO clients (id) VALUES (?)";
        } elseif ($role === "vendeur") {
            $sql2 = "INSERT INTO vendeurs (id) VALUES (?)";
        } else {
            $error = "Rôle invalide.";
        }

        if (!$error) {
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("i", $new_id);
            $stmt2->execute();
            $success = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Créer un compte - Agora Francia</title>
</head>
<body>
<h1>Créer un compte</h1>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color:green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="post" action="creer_compte.php">
    <label>Nom :</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" required><br><br>

    <label>Email :</label><br>
    <input type="email" name="email" required><br><br>

    <label>Mot de passe :</label><br>
    <input type="password" name="mot_de_passe" required><br><br>

    <label>Rôle :</label><br>
    <select name="role" required>
        <option value="client">Client</option>
        <option value="vendeur">Vendeur</option>
    </select><br><br>

    <button type="submit">Créer un compte</button>
</form>

<p><a href="login.php">Déjà un compte ? Connectez-vous ici</a></p>

</body>
</html>
