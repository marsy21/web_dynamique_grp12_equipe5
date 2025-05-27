<?php
$servername = "localhost";
$username = "root";
$password = ""; // Mets ton mot de passe MySQL ici si nécessaire
$dbname = "Agora_Francia";

// Crée la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>
