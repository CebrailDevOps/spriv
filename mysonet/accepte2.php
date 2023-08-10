<?php
include 'session.php';
if (isset($_GET['ref_demande']) && isset($pseudo)) {
    $ref_demande = $_GET['ref_demande'];
    include 'db.php';
    $stmt = $conn->prepare("SELECT demandeur, ip_demandeur FROM demandes_recues WHERE ref_demande = :ref_demande");
    $stmt->bindParam(':ref_demande', $ref_demande);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $demandeur = $row['demandeur'];
    $ip_demandeur = $row['ip_demandeur'];
    if (isset($_GET['token'])) {
        $stmt4 = $conn->prepare("INSERT INTO mes_amis (pseudo,ip_add,token) VALUES (?, ?, ?)");
        $stmt4->execute([$demandeur, $ip_demandeur, $_GET['token']]);
    }
    $stmt3 = $conn->prepare("DELETE FROM demandes_recues WHERE ref_demande = :ref_demande");
    $stmt3->bindParam(':ref_demande', $ref_demande);
    $stmt3->execute();

    include 'notifier.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Demande acceptée - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include 'navbarNotif.php'; ?>
<div class="container">
<?php
    echo "Vous avez accepté la demande d'ami.<br>Vous allez être redirigé...";
    echo "<script>setTimeout(function(){window.location.href = 'notif.php';}, 5000);</script>";
}
?>
</div>
</body>
</html>