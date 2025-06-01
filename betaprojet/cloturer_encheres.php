<?php
session_start();
include 'db.php';
include 'includes/fonctions/notifications.php';
include 'envoyer_notification.php';


date_default_timezone_set('Europe/Paris');

// 1. Sélectionner tous les articles type enchère expirés et non vendus
$sql = "
    SELECT id, nom, id_vendeur 
    FROM articles 
    WHERE type_vente = 'meilleure offre' 
      AND vendu = 0 
      AND date_fin_enchere IS NOT NULL 
      AND date_fin_enchere < NOW()
";

$res = mysqli_query($db, $sql);

while ($article = mysqli_fetch_assoc($res)) {
    $article_id = $article['id'];
    $nom_article = $article['nom'];
    $vendeur_id = $article['id_vendeur'];

    // 2. Récupérer les meilleures offres
    $sql_ench = "
        SELECT client_id, montant 
        FROM encheres 
        WHERE article_id = $article_id 
        ORDER BY montant DESC
    ";
    $ench_res = mysqli_query($db, $sql_ench);
    $ench_list = [];

    while ($row = mysqli_fetch_assoc($ench_res)) {
        $ench_list[] = $row;
    }

    if (count($ench_list) === 0) {
        // Aucun enchérisseur : on ne vend pas
        ajouterNotification($db, $vendeur_id, "Aucune enchère reçue", null, null, "meilleure offre", "L'enchère pour '$nom_article' est terminée sans offre.");
        continue;
    }

    // 3. Calcul prix final
    $gagnant = $ench_list[0];
    $second_prix = isset($ench_list[1]) ? $ench_list[1]['montant'] : $gagnant['montant'] - 1;
    $prix_final = $second_prix + 1;

    // 4. Notifications
    $gagnant_id = $gagnant['client_id'];
    ajouterNotification($db, $gagnant_id, "Félicitations !", null, null, "meilleure offre", "Vous avez remporté l'enchère pour '$nom_article' à $prix_final €.");
    ajouterNotification($db, $vendeur_id, "Vente réussie", null, null, "meilleure offre", "Vous avez vendu '$nom_article' à $prix_final €.");

    // Notifier les perdants
    for ($i = 1; $i < count($ench_list); $i++) {
        ajouterNotification($db, $ench_list[$i]['client_id'], "Enchère perdue", null, null, "meilleure offre", "Vous avez perdu l'enchère pour '$nom_article'.");
    }

    // 5. Marquer l'article comme vendu
    mysqli_query($db, "UPDATE articles SET vendu = 1 WHERE id = $article_id");
}

echo "✅ Traitement des enchères terminé.";
?>
