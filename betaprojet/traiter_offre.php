<?php
session_start();
include 'db.php';
require_once 'includes/fonctions/notifications.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offre_id = intval($_POST['offre_id']);
    $acheteur_id = intval($_POST['acheteur_id']);
    $article_id = intval($_POST['article_id']);
    $action = $_POST['action'];

    if ($action === 'accepter') {
        // Valider la négociation
        $stmt = $db->prepare("UPDATE negociations SET statut = 'accepte' WHERE id = ?");
        $stmt->bind_param("i", $offre_id);
        $stmt->execute();

        // Marquer l'article comme vendu
        $stmt2 = $db->prepare("UPDATE articles SET vendu = 1 WHERE id = ?");
        $stmt2->bind_param("i", $article_id);
        $stmt2->execute();

        // Notifier l'acheteur
        envoyer_notification($db, $acheteur_id, "🎉 Votre offre a été acceptée pour l’article n°$article_id !");

    } elseif ($action === 'refuser') {
        // Récupérer le nombre de tours déjà échangés
        $res = mysqli_query($db, "
            SELECT COUNT(*) AS total 
            FROM negociations 
            WHERE article_id = $article_id AND acheteur_id = $acheteur_id
        ");
        $data = mysqli_fetch_assoc($res);
        $tentatives = intval($data['total']);

        if ($tentatives >= 5) {
            // Clôturer la négociation
            $stmt = $db->prepare("UPDATE negociations SET statut = 'refuse' WHERE id = ?");
            $stmt->bind_param("i", $offre_id);
            $stmt->execute();

            envoyer_notification($db, $acheteur_id, "❌ La négociation pour l’article n°$article_id a échoué (5 tentatives).");
        } else {
            envoyer_notification($db, $acheteur_id, "❌ Votre offre a été refusée. Vous pouvez proposer une nouvelle offre.");
        }
    }

    header("Location: notifications.php");
    exit;
}
?>
