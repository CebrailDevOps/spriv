<?php
session_start();

if (!isset($_SESSION['pseudo'])) {
    header("Location: index.php");
    exit();
}

include 'session.php';
include 'db.php';

// Récupération du token de l'utilisateur actuellement connecté
$token_stmt = $conn->prepare("SELECT token FROM login WHERE pseudo = :pseudo");
$token_stmt->bindParam(':pseudo', $pseudo);
$token_stmt->execute();
$user_token = $token_stmt->fetchColumn(); // Récupère le token de l'utilisateur

// Récupérer les amis et les adresses IP
$stmt = $conn->query("SELECT * FROM mes_amis");
$amis = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'notifier.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Publications des amis - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php include 'navbarNotif.php'; ?>

    <div class="container">
        <h1>Publications des amis</h1>
        <div id="postes_amis"></div>
    </div>
</body>
</html>
<script>
    // Les amis et les adresses IP récupérées à partir de PHP
    let amis = <?php echo json_encode($amis); ?>;
    let userToken = <?php echo json_encode($user_token); ?>;

    function timeSince(datePubli) {
        let parts = datePubli.split(' ');
        let dateParts = parts[0].split('-');
        let timeParts = parts[1].split(':');

        let date = new Date(
            dateParts[0], // année
            dateParts[1] - 1, // mois (0-indexé)
            dateParts[2], // jour
            timeParts[0], // heure
            timeParts[1], // minute
            timeParts[2] // seconde
        );

        const seconds = Math.floor((new Date() - date) / 1000);

        if (seconds < 60) return "il y a " + seconds + "s";
        if (seconds < 3600) return "il y a " + Math.floor(seconds / 60) + "min";
        if (seconds < 86400) return "il y a " + Math.floor(seconds / 3600) + "h";
        if (seconds < 604800) return "il y a " + Math.floor(seconds / 86400) + "j";
        if (seconds < 2592000) return "il y a " + Math.floor(seconds / 604800) + "sem";
        if (seconds < 31536000) return "il y a " + Math.floor(seconds / 2592000) + " mois";
        if (seconds === 31536000) return "il y a 1 an";
        return "il y a " + Math.floor(seconds / 31536000) + " ans";
    }

    amis.forEach(ami => {
        fetch(`http://${ami.ip_add}/mes_postes.php?token=${userToken}`)
            .then(response => response.json())
            .then(postes => {
                let posteDiv = document.getElementById('postes_amis');
                postes.forEach(poste => {
                    let p = document.createElement('p');
                    p.className = 'poste';
                    posteDiv.appendChild(p);
                    let strong = document.createElement('strong');
                    strong.textContent = ami.pseudo + ' ' + timeSince(poste.date_publication) + ': ';
                    p.appendChild(strong);
                    let span = document.createElement('span');
                    span.textContent =  poste.contenu;
                    p.appendChild(span);
                });
            })
            .catch(error => console.error('Erreur:', error));
    });
</script>