<?php
if (isset($_GET['ref_demande'])) {
    $ref_demande = $_GET['ref_demande'];
    $stmt = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = ?");
    $stmt->bindParam(':ref_demande', $ref_demande)
    $stmt->execute();

    echo "Vous avez accepté la demande d'ami. Redirection dans 3 secondes";
    echo "<script>setTimeout(function(){window.location.href = 'notif.php';}, 3000);</script>";
}
?>