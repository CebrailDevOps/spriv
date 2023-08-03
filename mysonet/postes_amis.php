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

// Assurez-vous de vérifier le chemin d'accès au fichier et de le modifier si nécessaire
$file_path = '/home/inspectorsonet/demandes_en_attente';

if (file_exists($file_path)) {
    // Lire le fichier dans un tableau
    $lines = file($file_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Parcourir chaque ligne du fichier
    foreach ($lines as $line) {
        // Diviser la ligne pour obtenir les détails de la demande
        list($ref_demande, $pseudo_demandeur, $ip_demandeur, $date_et_heure) = explode(';', $line);

        // Préparer la requête SQL pour insérer la demande dans la base de données
        $stmt = $conn->prepare("INSERT INTO demandes_recues (ref_demande, demandeur, ip_demandeur, date_demande)
                                VALUES (:ref_demande, :demandeur, :ip_demandeur, :date_demande)");

        // Exécuter la requête SQL
        $stmt->execute([
            ':ref_demande' => $ref_demande,
            ':demandeur' => $pseudo_demandeur,
            ':ip_demandeur' => $ip_demandeur,
            ':date_demande' => $date_et_heure
        ]);
    }

    // Une fois que toutes les demandes sont traitées, vous pouvez vider le fichier
    file_put_contents($file_path, '');
}

// Récupération du nombre de demandes d'ami non traitées
$stmt = $conn->query("SELECT COUNT(*) FROM demandes_recues WHERE statut = 'répondre'");
$demandes_ami = $stmt->fetchColumn();
?>

<html>
<head>
    <title>Publications des amis</title>
</head>
<body>

    <?php
    // Si le nombre de demandes d'ami est supérieur à 0, afficher le message
    if ($demandes_ami > 0) {
        echo "<p><a href='notif.php'>Vous avez reçu " . $demandes_ami . ($demandes_ami > 1 ? " demandes d'ami." : " demande d'ami.") . "</a></p>";
    }
    ?>

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
