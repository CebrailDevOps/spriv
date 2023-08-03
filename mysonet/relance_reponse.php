<?php
    // Commencer la session
    session_start();

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=db;dbname=mysonet', 'mysonet', '123456a.');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer toutes les demandes en attente de reconnexion
    $stmt = $pdo->prepare("SELECT demandeur, ip_demandeur, ref_demande FROM demandes_recues WHERE statut = 'En attente de reconnexion'");
    $stmt->execute();
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Parcourir toutes les demandes
    foreach($demandes as $demande) {
        // Pinger l'IP avec un délai d'attente de 2 secondes
        exec("ping -c 1 -W 2 " . escapeshellarg($demande['ip_demandeur']), $output, $result);

        // Si le ping est OK, connectez-vous en ssh et ajoutez les informations dans le fichier
        if ($result == 0) {
            $stmt = $conn->prepare("SELECT token FROM login");
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->execute();
            $token = $stmt->fetchColumn();
            
            $ip_add_full = shell_exec("hostname -I");
            $ip_add_array = explode(' ', $ip_add_full);
            $ip_add = $ip_add_array[0]; // Prend la première adresse IP
            header('Location: http://'.$demande['ip_demandeur'].'/accepte.php?ref_demande='.$demande['ref_demande'].'&ip_add='. $ip_add.'&token='. $token);
        }
    }
?>