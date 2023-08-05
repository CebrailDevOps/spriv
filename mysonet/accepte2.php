<?php
if (isset($_GET['ref_demande'])) {
    include 'db.php';
    try {
        $stmt = $conn->prepare("SELECT demandeur, ip_demandeur FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $demandeur = $row['demandeur'];
        $ip_demandeur = $row['ip_demandeur'];
        $stmt3 = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt3->bindParam(':ref_demande', $ref_demande);
        $stmt3->execute();
        if (isset($_GET['token'])) {
            $stmt4 = $conn->prepare("INSERT INTO mes_amis (pseudo,ip_add,token) VALUES (?, ?, ?)");
            $stmt4->execute([$demandeur, $ip_demandeur, $_GET['token']]);
        }
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit(); // Arrêter l'exécution si la connexion échoue
    }
    echo "Vous avez accepté la demande d'ami.<br>Vous allez être redirigé...";
    echo "<script>setTimeout(function(){window.location.href = 'notif.php';}, 3000);</script>";
}
?>