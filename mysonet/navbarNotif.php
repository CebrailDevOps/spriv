<div class="header"><?php echo $pseudo; ?> - MySoNet.Online</div>
<div class="navbar">
    <a href="postes_amis.php">Publications des amis</a>
    <a href="mes_publications.php">Mes publications</a>
    <?php if ($demandes_ami > 0) {
        echo '<a href="notif.php" class="notif-link"><span class="notif-icon">🔔</span><span class="notif-count">'.$demandes_ami.'</span></a>';
    } ?>
</div>
