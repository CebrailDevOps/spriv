<?php
header('Access-Control-Allow-Origin: *');
include 'session.php';
include 'db.php';

// Vérifier si le token du client est dans la table des amis
if (!isset($_GET['token'])) {
    http_response_code(403);
    echo "Forbidden: No token provided.";
    exit;
}

$token = $_GET['token'];
$stmt = $conn->prepare("SELECT * FROM mes_amis WHERE token = :token");
$stmt->bindParam(':token', $token);
$stmt->execute();

// Si le token n'est pas dans la table des amis, renvoyer une erreur
if ($stmt->rowCount() === 0) {
    http_response_code(403);
    echo "Forbidden: You are not a friend.";
    exit;
}

// Si le token est dans la table des amis, récupérer les postes
$stmt2 = $conn->query("SELECT * FROM mes_postes ORDER BY date_publication DESC");

$postes = array();
while ($row = $stmt2->fetch()) {
    $postes[] = $row;
}

header('Content-Type: application/json');
echo json_encode($postes);
?>
