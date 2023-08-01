<?php
if (isset($_POST['ref_demande'])) {
    $ref_demande = $_POST['ref_demande'];

    // connexion à la base de données
    $servername = "localhost";
    $username = "root";
    $password = "123456a.";
    $dbname = "mysonet";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Modifier la demande d'ami
        $stmt = $conn->prepare("UPDATE FROM demandes_recues SET statut = 'En attente de reconnexion' WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
        
        $stmt = $conn->prepare("SELECT ip_demandeur FROM demandes_recues WHERE token = ?");
        $stmt->bindParam(':ref_demande', $ref_demande)
        $stmt->execute([$ip_demandeur]);

        $stmt = $conn->prepare("SELECT * FROM login WHERE token = ?");
        $stmt->execute([$token]);
        
        $ip_add=shell_exec("hostname -I");
        header('Location: http://'.$ip_demandeur.'/accepte.php?ref_demande='.$ref_demande.'&ip_add='. $ip_add.'&token='. $token);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
?>
