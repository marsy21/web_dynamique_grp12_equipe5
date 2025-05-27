<?php
session_start();
include("connexion.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateur WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $utilisateur = $result->fetch_assoc();

    if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
        $_SESSION['role'] = $utilisateur['role']; // utile si besoin

        // Exemple : redirection selon rôle
        if ($utilisateur['role'] == 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: votrecompte.php");
        }
        exit();
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>
