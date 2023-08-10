<?php
include 'session.php';
if (!isset($pseudo)) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['ref_demande'])) {
    $ref_demande = $_POST['ref_demande'];

    // connexion à la base de données
    include 'db.php';

    // Supprimer la demande d'ami
    $stmt = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = :ref_demande");
    $stmt->bindParam(':ref_demande', $ref_demande);
    $stmt->execute();

    $ip_add=shell_exec("hostname -I");
    include 'monip.php';
    header('Location: http://'.$monip.'/reponse.php?ref_demande='.$ref_demande.'&ip_add='. $ip_add);
}
?>

