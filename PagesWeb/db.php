<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'agora_francia';

$db = mysqli_connect($host, $user, $password, $database);

if (!$db) {
    die("Échec de la connexion à la base de données : " . mysqli_connect_error());
}
?>
