<?php
include 'session.php';

if (!isset($pseudo) || !isset($_POST['poste_id'])) {
    header("Location: index.php");
    exit();
}

$poste_id = $_POST['poste_id'];

include 'db.php';

$stmt = $conn->prepare("SELECT contenu FROM mes_postes WHERE ID = :poste_id");
$stmt->bindParam(':poste_id', $poste_id);
$stmt->execute();

$poste_contenu = $stmt->fetchColumn();

include 'notifier.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier mon poste - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbarNotif.php'; ?>

<div class="container">
    <h1>Modifier mon poste</h1>
    <form action="sauvegarder_modification.php" method="post">
        <input type="hidden" name="poste_id" value="<?php echo $poste_id; ?>" />
        <textarea name="contenu"><?php echo $poste_contenu; ?></textarea>
        <input type="submit" value="Sauvegarder" />
    </form>
</div>
</body>
</html>