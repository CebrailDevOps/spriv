<?php
session_start();

if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "123456a.";
$dbname = "mysonet";

try {
    // Créer une connexion
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des demandes d'ami
    $stmt = $conn->prepare("SELECT * FROM demandes_recues WHERE statut = 'répondre' ORDER BY date_demande DESC");
    $stmt->execute();

    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<html>
<head>
    <title>Notifications</title>
    <style>
        .demande-ami {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 10px;
        }
        .demande-ami p {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <p><a href="mes_publications.php">Mes publications</a></p>

    <p><a href="postes_amis.php">Publications des amis</a></p>

    <h1>Notifications</h1>
    <?php
    foreach($demandes as $demande){
        echo "<div class='demande-ami'>";
        echo "<p>Demande d'ami de " . $demande["demandeur"]. "</p>";
        echo "<form method='post' action='accepter_demande.php'>";
        echo "<input type='hidden' name='ref_demande' value='" . $demande["ref_demande"] . "'>";
        echo "<button type='submit' name='accepter'>Accepter</button>";
        echo "</form>";
        echo "<form method='post' action='refuser_demande.php'>";
        echo "<input type='hidden' name='ref_demande' value='" . $demande["ref_demande"] . "'>";
        echo "<button type='submit' name='refuser'>Refuser</button></div>";
        echo "</form></div>";
    }
    ?>
</body>
</html>
