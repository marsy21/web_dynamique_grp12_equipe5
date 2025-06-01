<?php
session_start();
include 'db.php';

if (!isset($_SESSION['utilisateur']['id'])) {
    header("Location: connexion.php");
    exit;
}

$nom = mysqli_real_escape_string($db, $_POST['nom']);
$description = mysqli_real_escape_string($db, $_POST['description']);
$prix = floatval($_POST['prix_initial']);
$categorie = $_POST['categorie'];
$rarete = $_POST['rarete'];
$type_vente = $_POST['type_vente'];
$id_vendeur = $_SESSION['utilisateur']['id'];

$duree_enchere = null;
$date_fin_enchere = null;
if ($type_vente === 'meilleure offre') {
    $duree_enchere = intval($_POST['duree_enchere']);
    $date_fin_enchere = date('Y-m-d H:i:s', time() + $duree_enchere * 60);
}

// Image
$target_dir = "Articles/Images/";
$image_name = basename($_FILES["image"]["name"]);
$target_file = $target_dir . $image_name;
move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);

// Insertion dans articles
$stmt = $db->prepare("
    INSERT INTO articles (nom, description, prix_initial, categorie, rarete, type_vente, id_vendeur, duree_enchere, date_fin_enchere)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssdsssiss", $nom, $description, $prix, $categorie, $rarete, $type_vente, $id_vendeur, $duree_enchere, $date_fin_enchere);
$stmt->execute();
$article_id = $stmt->insert_id;
$stmt->close();

// Insertion dans table photos
$stmt = $db->prepare("INSERT INTO photos (article_id, url) VALUES (?, ?)");
$stmt->bind_param("is", $article_id, $image_name);
$stmt->execute();
$stmt->close();

header("Location: votrecompte.php");
exit;
?>
