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
    <title>Mes publications</title>
</head>
<body>
    <?php
    // Si le nombre de demandes d'ami est supérieur à 0, afficher le message
    if ($demandes_ami > 0) {
        echo "<p><strong>" . $poste["date_publication"]. "</strong>: " . $poste["contenu"];
    }
    ?>

    <p><a href="postes_amis.php">Publications des amis</a></p>

    <h1>Mes publications</h1>
    <?php
    foreach($mes_postes as $poste){
        echo "<p><strong>" . $poste["date_publication"]. "</strong>: " . $poste["contenu"]. "</p>";
        echo "<form action='modifier_poste.php' method='post' style='display:inline;'>
            <input type='hidden' name='poste_id' value='" . $poste["id"] . "' />
            <input type='submit' value='Modifier' />
        </form>";
        echo "<form action='supprimer_poste.php' method='post' style='display:inline;'>
                <input type='hidden' name='poste_id' value='" . $poste["id"] . "' />
                <input type='submit' value='Supprimer' />
            </form></p>";
    }
    ?>
</body>
</html>

