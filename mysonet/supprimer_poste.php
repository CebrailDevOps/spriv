<?php
include 'session.php';

if (!isset($pseudo)) {
    header("Location: index.php");
    exit();
}

if (isset($_POST['poste_id'])) {
    $poste_id = $_POST['poste_id'];

    include 'db.php';

    // Vérifiez que l'utilisateur supprime bien son propre poste
    $stmt = $conn->prepare("DELETE FROM mes_postes WHERE ID = :poste_id");
    $stmt->bindParam(':poste_id', $poste_id);
    $stmt->execute();

    header("Location: mes_publications.php");
}
?>