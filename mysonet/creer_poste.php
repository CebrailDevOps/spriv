<?php
include 'session.php';

if (!isset($pseudo) || empty($_POST['contenu'])) {
    header("Location: mes_publications.php");
    exit();
}

include 'db.php';
$contenu = $_POST['contenu'];
$date_publication = date('Y-m-d H:i:s'); // Vous pouvez ajuster le format de la date si nécessaire

$stmt = $conn->prepare("INSERT INTO mes_postes (contenu) VALUES (:contenu)");
$stmt->execute([':contenu' => $contenu]);

header("Location: mes_publications.php");
?>