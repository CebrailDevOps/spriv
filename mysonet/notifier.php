<?php
// Assurez-vous de vérifier le chemin d'accès au fichier et de le modifier si nécessaire
$file_path = '/home/inspectorsonet/demandes_en_attente';

if (file_exists($file_path)) {
    // Lire le fichier dans un tableau
    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Parcourir chaque ligne du fichier
    foreach ($lines as $line) {
        // Diviser la ligne pour obtenir les détails de la demande
        list($ref_demande, $pseudo_demandeur, $ip_demandeur, $date_et_heure) = explode(';', $line);

        // Préparer la requête SQL pour insérer la demande dans la base de données
        $stmt2 = $conn->prepare("INSERT INTO demandes_recues (ref_demande, demandeur, ip_demandeur, date_demande)
                                VALUES (:ref_demande, :demandeur, :ip_demandeur, :date_demande)");

        // Exécuter la requête SQL
        $stmt2->execute([
            ':ref_demande' => $ref_demande,
            ':demandeur' => $pseudo_demandeur,
            ':ip_demandeur' => $ip_demandeur,
            ':date_demande' => $date_et_heure
        ]);
    }

    // Une fois que toutes les demandes sont traitées, vous pouvez vider le fichier
    file_put_contents($file_path, '');
}

// Récupération du nombre de demandes d'ami non traitées
$stmt3 = $conn->query("SELECT COUNT(*) FROM demandes_recues WHERE statut = 'répondre'");
$demandes_ami = $stmt3->fetchColumn();
?>