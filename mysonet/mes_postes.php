<?php
    header('Access-Control-Allow-Origin: *');

    $servername = "localhost";
    $username = "root";
    $password = "123456a.";
    $dbname = "mysonet";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si le token du client est dans la table des amis
        if (!isset($_GET['token'])) {
            http_response_code(403);
            echo "Forbidden: No token provided.";
            exit;
        }
        
        $token = $_GET['token'];
        $stmt = $conn->prepare("SELECT * FROM mes_amis WHERE token = ?");
        $stmt->execute([$token]);

        // Si le token n'est pas dans la table des amis, renvoyer une erreur
        if ($stmt->rowCount() === 0) {
            http_response_code(403);
            echo "Forbidden: You are not a friend.";
            exit;
        }

        // Si le token est dans la table des amis, récupérer les postes
        $stmt = $conn->query("SELECT * FROM mes_postes ORDER BY date_publication DESC");

        $postes = array();
        while ($row = $stmt->fetch()) {
            $postes[] = $row;
        }

        header('Content-Type: application/json');
        echo json_encode($postes);

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
?>