<?php
include 'session.php';

if (!isset($pseudo)) {
    header("Location: index.php");
    exit();
}

include 'db.php';
include 'notifier.php';
$stmt = $conn->prepare("SELECT * FROM mes_amis ORDER BY pseudo");
$stmt->execute();

$amis = $stmt->fetchAll(PDO::FETCH_ASSOC); 

?>

<!DOCTYPE html>
<html>
<head>
    <title>Mes amis - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbarNotif.php'; ?>

<div class="container">
    <h1>Mes amis</h1>
        <?php
            foreach($amis as $ami) {
                echo "<p><strong>" . $ami["pseudo"] . "</strong></p>";
            }
        ?>
</div>

</body>
</head>