<?php
include 'session.php';

if (!isset($pseudo) || !isset($_POST['poste_id']) || !isset($_POST['contenu'])) {
    header("Location: index.php");
    exit();
}

$poste_id = $_POST['poste_id'];
$contenu = $_POST['contenu'];

include 'db.php';

$stmt = $conn->prepare("UPDATE mes_postes SET contenu = :contenu WHERE ID = :poste_id");
$stmt->bindParam(':contenu', $contenu);
$stmt->bindParam(':poste_id', $poste_id);
$stmt->execute();

header("Location: mes_publications.php");
?>
