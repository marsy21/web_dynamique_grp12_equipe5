<?php
function envoyer_notification($db, $utilisateur_id, $message) {
    $stmt = $db->prepare("INSERT INTO notifications (utilisateur_id, message, date_creation) VALUES (?, ?, NOW())");
    $stmt->bind_param("is", $utilisateur_id, $message);
    $stmt->execute();
    $stmt->close();
}
