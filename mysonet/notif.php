<?php
include 'session.php';

if (!isset($pseudo)) {
    header("Location: index.php");
    exit();
}

include 'db.php';

// Récupération des demandes d'ami
$stmt = $conn->prepare("SELECT * FROM demandes_recues WHERE statut = 'répondre' ORDER BY date_demande DESC");
$stmt->execute();

$demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container">
    <h1>Notifications</h1>
    <?php
    foreach($demandes as $demande){
        echo "<div class='demande-ami'>";
        echo "<p>Demande d'ami de " . $demande["demandeur"]. "</p>";
        echo "<form method='post' action='accepter_demande.php'>";
        echo "<input type='hidden' name='ref_demande' value='" . $demande["ref_demande"] . "'>";
        echo "<button type='submit' name='accepter'>✅</button>";
        echo "</form>";
        echo "<form method='post' action='refuser_demande.php'>";
        echo "<input type='hidden' name='ref_demande' value='" . $demande["ref_demande"] . "'>";
        echo "<button type='submit' name='refuser'>❌</button>";
        echo "</form></div>";
    }
    ?>
</div>
</body>
</html>
