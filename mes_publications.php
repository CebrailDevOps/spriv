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

    $pseudo = $_SESSION['pseudo'];

    // Récupération des posts de l'utilisateur actuellement connecté
    $stmt = $conn->prepare("SELECT * FROM mes_postes ORDER BY date_publication DESC");
    $stmt->execute();

    $mes_postes = $stmt->fetchAll(PDO::FETCH_ASSOC); 
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<html>
<head>
    <title>Mes publications</title>
</head>
<body>
    <p><a href="postes_amis.php">Publications des amis</a></p>
    <h1>Mes publications</h1>
    <?php
    foreach($mes_postes as $poste){
        echo "<p><strong>" . $poste["date_publication"]. "</strong>: " . $poste["contenu"]. "</p>";
    }
    ?>
</body>
</html>

