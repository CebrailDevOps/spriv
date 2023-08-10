<?php
include 'session.php';

if (!isset($pseudo)) {
    header("Location: index.php");
    exit();
}

include 'db.php';

// Récupération des posts de l'utilisateur actuellement connecté
$stmt = $conn->prepare("SELECT * FROM mes_postes ORDER BY date_publication DESC");
$stmt->execute();

$mes_postes = $stmt->fetchAll(PDO::FETCH_ASSOC); 

include 'notifier.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Mes publications - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbarNotif.php'; ?>

<div class="container">
    <h1>Mes publications</h1>
    <!-- Formulaire pour créer un nouveau poste -->
    <form action="creer_poste.php" method="post">
        <textarea name="contenu" placeholder="Quoi de neuf ?" rows="4"></textarea>
        <br>
        <input type="submit" value="Publier">
    </form>

    <?php
    include 'datePublication.php';
    foreach($mes_postes as $poste) {
        echo "<div class='poste'>";
        echo "<form action='modifier_poste.php' method='post' style='display:inline;'>
            <input type='hidden' name='poste_id' value='" . $poste["ID"] . "' />
            <input type='submit' value='📝' class='edit-button' />
        </form>";
        echo "<form action='supprimer_poste.php' method='post' style='display:inline;'>
                <input type='hidden' name='poste_id' value='" . $poste["ID"] . "' />
                <input type='submit' value='❌' class='delete-button' />
            </form></p>";
        echo "<p><strong>" . tempsDepuisPublication($poste["date_publication"]) . "</strong>: " . $poste["contenu"]. "</p>";
        echo "</div>";
    }
    ?>
</div>
</body>
</html>