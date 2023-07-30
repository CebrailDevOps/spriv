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

    // Récupération du token de l'utilisateur actuellement connecté
    $token_stmt = $conn->prepare("SELECT token FROM login WHERE pseudo = :pseudo");
    $token_stmt->bindParam(':pseudo', $pseudo);
    $token_stmt->execute();

    $user_token = $token_stmt->fetchColumn(); // Récupère le token de l'utilisateur

    // Récupérer les amis et les adresses IP
    $stmt = $conn->query("SELECT * FROM mes_amis");
    $amis = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<html>
<head>
    <title>Publications des amis</title>
</head>
<body>
    <p><a href="mes_publications.php">Mes publications</a></p>
    <h1>Publications des amis</h1>
    <div id='postes_amis'></div>
    <script>
    // Les amis et les adresses IP récupérées à partir de PHP
    let amis = <?php echo json_encode($amis); ?>;
    let userToken = <?php echo json_encode($user_token); ?>;

    amis.forEach(ami => {
        fetch(`http://${ami.ip_add}/mes_postes.php?token=${userToken}`)
            .then(response => response.json())
            .then(postes => {
                let posteDiv = document.getElementById('postes_amis');
                postes.forEach(poste => {
                    let p = document.createElement('p');
                    posteDiv.appendChild(p);
                    let strong = document.createElement('strong');
                    strong.textContent = ami.pseudo + ' ' + poste.date_publication + ': ';
                    p.appendChild(strong);
                    let span = document.createElement('span');
                    span.textContent =  poste.contenu;
                    p.appendChild(span);
                });
            })
            .catch(error => console.error('Erreur:', error));
    });
    </script>
</body>
</html>
