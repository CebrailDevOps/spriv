<?php
if (isset($_GET['ref_demande'])) {
    $ref_demande = $_GET['ref_demande'];
    $servername = "localhost";
    $username = "root";
    $password = "123456a.";
    $dbname = "mysonet";
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit(); // Arrêter l'exécution si la connexion échoue
    }
    echo "Vous avez accepté la demande d'ami. Redirection dans 3 secondes";
    echo "<script>setTimeout(function(){window.location.href = 'notif.php';}, 3000);</script>";
}
?>