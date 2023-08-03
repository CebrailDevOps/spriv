<?php
session_start();
if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}
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

        // Supprimer la demande d'ami
        $stmt = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();

        $ip_add=shell_exec("hostname -I");
        header('Location: http://10.0.10.231/reponse.php?ref_demande='.$ref_demande.'&ip_add='. $ip_add);
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
?>

