<?php
if (isset($_GET['ref_demande'])) {
    include 'db.php';
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("SELECT demandeur FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
        $demandeur = $stmt->fetchColumn();
        $stmt = $conn->prepare("SELECT ip_demandeur FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
        $ip_demandeur = $stmt->fetchColumn();
        $stmt = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
        if (isset($_GET['token'])) {
            $stmt = $conn->prepare("INSERT INTO mes_amis (pseudo,ip_add,token) VALUES (:demandeur,:ip_demandeur,:token)");
            $stmt->bindParam(':demandeur', $demandeur);
            $stmt->bindParam(':ip_demandeur', $ip_demandeur);
            $stmt->bindParam(':token', $_GET['token']);
            $stmt->execute();
        }
        $stmt = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit(); // Arrêter l'exécution si la connexion échoue
    }
    echo "Vous avez accepté la demande d'ami.<br>Vous allez être redirigé...";
    echo "<script>setTimeout(function(){window.location.href = 'notif.php';}, 3000);</script>";
}
?>