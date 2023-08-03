<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['ref_demande'])) {
    $ref_demande = $_POST['ref_demande'];
    $pseudo = $_SESSION['pseudo'];

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
        
        $stmt = $conn->prepare("SELECT ip_demandeur FROM demandes_recues WHERE ref_demande = ?");
        $stmt->bindParam(':ref_demande', $ref_demande)
        $stmt->execute();
        $ip_demandeur = $stmt->fetchColumn();

        // Vérifiez si l'IP est valide en envoyant un ping
        exec("ping -c 1 -W 2 " . $ip_demandeur, $output, $result);

        if ($result != 0) {
            echo "Vous avez accepté la demande d'ami. Vous pourrez voir les publications de votre nouvel ami seulement quand son serveur est en ligne.";
            echo "<script>setTimeout(function(){window.location.href = 'notif.php';}, 3000);</script>";
        }
        else {
            $stmt = $conn->prepare("SELECT token FROM login WHERE pseudo = ?");
            $stmt->bindParam(':ref_demande', $pseudo)
            $stmt->execute();
            $token = $stmt->fetchColumn();
            
            $ip_add=shell_exec("hostname -I");
            header('Location: http://'.$ip_demandeur.'/accepte.php?ref_demande='.$ref_demande.'&ip_add='. $ip_add.'&token='. $token);
        }
        
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
?>
