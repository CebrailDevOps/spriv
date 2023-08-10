<?php
$servername = "localhost";
$username = "root";
$password = "123456a.";
$dbname = "mysonet";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit(); // Arrêter l'exécution si la connexion échoue
}
?>