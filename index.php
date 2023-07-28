<!DOCTYPE html>
<html>
<head>
    <title>Login et visualisation des publications</title>
</head>
<body>
    <h1>Login</h1>

    <form method="POST" action="">
        <label for="pseudo">Pseudo:</label><br>
        <input type="text" id="pseudo" name="pseudo"><br>
        <label for="pwd">Mot de passe:</label><br>
        <input type="password" id="pwd" name="pwd"><br>
        <input type="submit" name="submit" value="Se connecter">
    </form>

    <?php
    // Votre serveur MariaDB, par défaut c'est généralement localhost
    $servername = "localhost";

    // Identifiants MariaDB
    $username = "root";
    $password = "123456a.";
    $dbname = "mysonet";

    try {
        // Créer une connexion
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Si le formulaire est soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $pseudo = $_POST["pseudo"];
            $pwd = $_POST["pwd"];

            // Requête préparée pour vérifier le login
            $stmt = $conn->prepare("SELECT * FROM login WHERE pseudo = :pseudo AND mot_de_passe = PASSWORD(:pwd)");
            $stmt->bindParam(':pseudo', $pseudo);
            $stmt->bindParam(':pwd', $pwd);
            $stmt->execute();

            // Si l'utilisateur est trouvé
            if ($stmt->rowCount() > 0) {
                echo "<h1>Mes publications</h1>";

                // Requête pour obtenir les publications
                $stmt = $conn->query("SELECT * FROM mes_postes ORDER BY date_publication DESC");

                // Afficher les publications
                while ($row = $stmt->fetch()) {
                    echo "<p><strong>" . $row["date_publication"]. "</strong>: " . $row["contenu"]. "</p>";
                }
                
                // Récupération du token de l'utilisateur actuellement connecté
                $token_stmt = $conn->prepare("SELECT token FROM login WHERE pseudo = :pseudo");
                $token_stmt->bindParam(':pseudo', $pseudo);
                $token_stmt->execute();

                $user_token = $token_stmt->fetchColumn(); // Récupère le token de l'utilisateur

                // Récupérer les amis et les adresses IP
                $stmt = $conn->query("SELECT * FROM mes_amis");
                $amis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo "<h1>Publications des amis</h1>";
                echo "<div id='postes_amis'></div>";

            } else {
                echo "Pseudo ou mot de passe incorrect.";
            }
        }
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>

</body>
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
                p.textContent = ami.pseudo + ' ' + poste.date_publication + ': ' + poste.contenu;
                posteDiv.appendChild(p);
            });
        })
        .catch(error => console.error('Erreur:', error));
});
</script>
</html>
