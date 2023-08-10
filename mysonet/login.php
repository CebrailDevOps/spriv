<?php
include 'session.php';
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pseudo = $_POST["pseudo"];
    $pwd = $_POST["pwd"];

    $stmt = $conn->prepare("SELECT * FROM login WHERE pseudo = :pseudo AND mot_de_passe = PASSWORD(:pwd)");
    $stmt->bindParam(':pseudo', $pseudo);
    $stmt->bindParam(':pwd', $pwd);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['pseudo'] = $pseudo;
        header('Location: postes_amis.php');
    } else {
        header('Location: index.php?error=1');
    }
}
?>
