<?php
session_start();

if (!isset($_SESSION['pseudo']) || !isset($_POST['poste_id'])) {
    header("Location: index.php");
    exit();
}

$poste_id = $_POST['poste_id'];

$servername = "localhost";
$username = "root";
$password = "123456a.";
$dbname = "mysonet";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $conn->prepare("SELECT contenu FROM mes_postes WHERE ID = :poste_id");
    $stmt->bindParam(':poste_id', $poste_id);
    $stmt->execute();

    $poste_contenu = $stmt->fetchColumn();

} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

<html>
<head>
    <title>Modifier le Poste</title>
</head>
<body>
    <form action="sauvegarder_modification.php" method="post">
        <input type="hidden" name="poste_id" value="<?php echo $poste_id; ?>" />
        <textarea name="contenu"><?php echo $poste_contenu; ?></textarea>
        <input type="submit" value="Sauvegarder" />
    </form>
</body>
</html>
