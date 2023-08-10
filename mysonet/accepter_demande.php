<?php
include 'session.php';
if (!isset($pseudo)) {
    header("Location: index.php");
    exit();
}
if (isset($_POST['ref_demande'])) {
    $ref_demande = $_POST['ref_demande'];

    include 'db.php';
    // Modifier la demande d'ami
    $stmt = $conn->prepare("UPDATE demandes_recues SET statut = 'En attente de reconnexion' WHERE ref_demande = :ref_demande");
    $stmt->bindParam(':ref_demande', $ref_demande);
    $stmt->execute();
    
    $stmt2 = $conn->prepare("SELECT ip_demandeur FROM demandes_recues WHERE ref_demande = :ref_demande");
    $stmt2->bindParam(':ref_demande', $ref_demande);
    $stmt2->execute();
    $ip_demandeur = $stmt2->fetchColumn();

    // Vérifiez si l'IP est valide en envoyant un ping
    exec("ping -c 1 -W 2 " . $ip_demandeur, $output, $result);

    if ($result != 0) { 
        include 'notifier.php' ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Demande acceptée - MySoNet.Online</title>
            <link rel="stylesheet" href="styles.css">
        </head>
        <body>
        <?php include 'navbarNotif.php'; ?>
        <div class="container">
            <p>Vous avez accepté la demande d'ami.<br>Vous verrez ses publications quand son serveur sera en ligne.
            <br>Vous allez être redirigé...</p>
            <script>setTimeout(function(){window.location.href = 'notif.php';}, 5000);</script>
        </div>
        </body>
        </html>
    <?php }
    else {
        $stmt3 = $conn->prepare("SELECT token FROM login WHERE pseudo = :pseudo");
        $stmt3->bindParam(':pseudo', $pseudo);
        $stmt3->execute();
        $token = $stmt3->fetchColumn();
        
        $ip_add_full = shell_exec("hostname -I");
        $ip_add_array = explode(' ', $ip_add_full);
        $ip_add = $ip_add_array[0]; // Prend la première adresse IP
        header('Location: http://'.$ip_demandeur.'/accepte.php?ref_demande='.$ref_demande.'&ip_add='. $ip_add.'&token='. $token);
        exit();
    }
}
?>