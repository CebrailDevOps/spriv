#!/bin/bash

# Vérification si le script est exécuté avec les privilèges root
if [ "$(id -u)" != "0" ]; then
   echo "Ce script doit être exécuté en tant que root."
   exit 1
fi

echo "############"
echo "1 - Vous êtes sur le point d'installer MySoNet dans votre serveur personnel."
echo "2 - Votre serveur doit avoir un système d'exploitation Debian 11+ ou Raspberry Pi OS."
echo "Est-ce bien le cas ? Oui (o) | Non (n)"

while true; do
  read -r -p "Veuillez entrer o ou n: " reponse

  case $reponse in
      [Oo]* ) break;; # Continue l'installation
      [Nn]* ) exit;;  # Quitte le script
      * )     echo "Veuillez répondre uniquement par o ou n.";;
  esac
done

echo "############"
echo "1 - Ayez une IP publique ou un nom de domaine qui y redirige."
echo "2 - En utilisation réseau local, assurez-vous d'avoir une IP privée statique."
echo "Votre serveur est-il configuré correctement ? Oui (o) | Non (n)"

while true; do
  read -r -p "Veuillez entrer o ou n: " reponse

  case $reponse in
      [Oo]* ) break;; # Continue l'installation
      [Nn]* ) exit;;  # Quitte le script
      * )     echo "Veuillez répondre uniquement par o ou n.";;
  esac
done

echo "############"
echo "1 - Apache2, PHP 8.1 et MariaDB vont être installés."
echo "2 - Assurez-vous que rien puisse empêcher le bon fonctionnement de ces services."
echo "3 - Un utilisateur va être créer, inspectorsonet, pour vous mettre en lien avec un serveur principal MySoNet.Online."
echo "4 - Ceci afin d'utiliser les services proposés par le site web comme la recherche et demande de nouveaux amis."
echo "5 - Pour couper tout lien, vous pourrez toujours supprimer le fichier /home/inspectorsonet/.ssh/authorized_keys"
echo "6 - Pensez à mettre un mot de passe solide pour cet utilisateur lorsque ce sera demandé !"
echo "Acceptez-vous toutes ces conditions ? Oui (o) | Non (n)"

while true; do
  read -r -p "Veuillez entrer o ou n: " reponse

  case $reponse in
      [Oo]* ) break;; # Continue l'installation
      [Nn]* ) exit;;  # Quitte le script
      * )     echo "Veuillez répondre uniquement par o ou n.";;
  esac
done

# Mise à jour des paquets
echo "Mise à jour des paquets..."
sudo apt update

# Installation de MariaDB
echo "Installation de MariaDB et des dépendances nécessaires..."
sudo apt -y install gnupg git
sudo apt -y install mariadb-server

# Installation d'apache2
echo "Installation d'Apache2..."
sudo apt -y install apache2

# Création du répertoire mysonet
echo "Création du répertoire pour l'application..."
sudo mkdir /var/www/mysonet

# Configuration d'Apache
echo "Configuration d'Apache..."
sudo echo "<VirtualHost *:80>
    ServerAdmin $SUDO_USER@localhost
    ServerName mysonet
    ServerAlias www.mysonet.online
    DocumentRoot /var/www/mysonet
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>" > /etc/apache2/sites-available/mysonet.conf

sudo a2ensite mysonet.conf
sudo a2dissite 000-default.conf
sudo systemctl reload apache2

### Installer PHP
echo "Installation de PHP et des dépendances nécessaires..."
sudo apt -y install ca-certificates apt-transport-https software-properties-common wget curl lsb-release
curl -sSL https://packages.sury.org/php/README.txt | sudo bash -x
sudo apt update
sudo apt -y install php8.1
sudo apt -y install libapache2-mod-php8.1

# Redémarrer Apache pour appliquer les modifications
echo "Redémarrage d'Apache..."
sudo systemctl restart apache2

# Cloner le dépôt Git à l'emplacement souhaité
git clone https://github.com/CebrailDevOps/spriv.git

echo "Les dossiers et fichiers ont été téléchargés avec succès depuis le dépôt Git."

# Déplacer le contenu du dossier mysonet vers /var/www/mysonet
sudo mv spriv/mysonet/* /var/www/mysonet

# Déplacer le contenu du dossier scripts dans le répertoire actuel
mv spriv/scripts .
sudo chmod -R +x scripts

echo "Les fichiers ont été déplacés avec succès."

# Obtenir le répertoire actuel où le script d'installation est exécuté
CURRENT_DIR=$(pwd)

# Supprimer le répertoire du dépôt cloné
rm -rf spriv

### Création de l'utilisateur inspectorsonet
echo "Création du groupe et de l'utilisateur inspectorsonet..."
sudo groupadd -g 19133 inspectorsonet
sudo useradd -u 19133 -g inspectorsonet -m -s /bin/bash inspectorsonet

echo "L'utilisateur inspectorsonet a été créé avec succès."

# Demander le mot de passe à l'utilisateur et vérifier qu'il est saisi correctement deux fois
while true; do
    read -s -p "Veuillez entrer un mot de passe pour le nouvel utilisateur inspectorsonet : " password
    echo
    read -s -p "Veuillez confirmer le mot de passe : " password_confirm
    echo

    if [ "$password" = "$password_confirm" ]; then
        break
    else
        echo "Les mots de passe ne correspondent pas. Veuillez réessayer."
    fi
done

# Utiliser la commande 'chpasswd' pour définir le mot de passe
echo "inspectorsonet:$password" | sudo chpasswd

# Suppression des variables contenant les mots de passe
unset password password_confirm

echo "Le mot de passe de l'utilisateur inspectorsonet a été créé avec succès."

### Créer un ID MySonet avec inspectorsonet
echo "Création d'un ID MySoNet pour l'utilisateur inspectorsonet..."

# Demander l'ID MySoNet à l'utilisateur
while true; do
    read -p "Veuillez entrer un ID MySoNet (caractères alphanumériques et underscore uniquement) : " id_mysonet
    if [[ "$id_mysonet" =~ ^[a-zA-Z0-9_]+$ ]]; then
        break
    else
        echo "L'ID MySoNet contient des caractères non autorisés. Veuillez réessayer."
    fi
done

# Passage à l'utilisateur inspectorsonet et écriture de l'ID dans le fichier
sudo -u inspectorsonet bash -c "echo '$id_mysonet' > /home/inspectorsonet/idmysonet"

# Suppression de la variable contenant l'ID
unset id_mysonet

echo "L'ID MySoNet a été créé avec succès."

### Créer des fichiers avec inspectorsonet
echo "Création des fichiers demandes_en_attente et demandes_envoyees..."

# Passage à l'utilisateur inspectorsonet et création des fichiers
sudo -u inspectorsonet bash -c "touch /home/inspectorsonet/demandes_en_attente /home/inspectorsonet/demandes_envoyees"

echo "Les fichiers ont été créés avec succès."

### Ajouter la clé publique de MySoNet.Online pour inspectorsonet
echo "Ajout de la clé publique de MySoNet.Online pour l'utilisateur inspectorsonet..."

# Création du répertoire .ssh s'il n'existe pas déjà
sudo -u inspectorsonet mkdir -p /home/inspectorsonet/.ssh

# Ajout de la clé publique aux authorized_keys
echo "ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABgQDqey+Qbf6dRRG949DKsIyjhbtnZFnKrcwcJtKAi3Vfgp5EbiK4FQA1eKyy9Oi2mN9IX0VnFH8A9jx+vPtGXrle7910n8VeBt13FdtW9ENzKHUAZdYF4JT08VujvjmYCkU/1uMlnMePyLI5AnvDa0uIuc6z8TCEAhXzaNmggM4f2WHvi6fgd9pr45d4sauOW/cCKkPWML8eWaXTfF05QH1QPzUCs3DbMk73vzNIifr4eE2d6LGMrFtCdfsGJWoQh2yk+Y5LHukjxCBTjKl7ZkvoEiCJGOjMeAPdXJqXH1Om2JGrH7zCkY8B3xx80F5g7oLPba4azKttgJuPduChrciPG+Y0ACB1Z/HUJJxdDy74vs4N4JLM3IuGUSJfythXIpAWMynk3wAXbojcaXty46hO99NjYZ0n82HVZIKFX9yfNLiT4Bmwq/bWjRz7vyf4Gpj/8PbHRyqUZ2t2JjgFOsC8m8A5THBUc5Hh2F04wc27WDUxmsXgMCwye0EEegwwscU= mysonet@nfs1" | sudo -u inspectorsonet tee -a /home/inspectorsonet/.ssh/authorized_keys > /dev/null

# Ajuster les permissions du dossier .ssh et du fichier authorized_keys
sudo -u inspectorsonet chmod 700 /home/inspectorsonet/.ssh
sudo -u inspectorsonet chmod 600 /home/inspectorsonet/.ssh/authorized_keys

echo "La clé publique a été ajoutée avec succès."

### Modification du mot de passe de root dans MariaDB
echo "Modification du mot de passe de l'utilisateur root dans MariaDB..."

# Demander le mot de passe à l'utilisateur et vérifier qu'il est saisi correctement deux fois
while true; do
    read -s -p "Veuillez entrer un nouveau mot de passe pour l'utilisateur root de MariaDB : " mariadb_password
    echo
    read -s -p "Veuillez confirmer le nouveau mot de passe : " mariadb_password_confirm
    echo

    if [ "$mariadb_password" = "$mariadb_password_confirm" ]; then
        break
    else
        echo "Les mots de passe ne correspondent pas. Veuillez réessayer."
    fi
done

# Utilisez la commande sed pour remplacer la ligne contenant le mot de passe
sed -i "s/\$password = \"\";/\$password = \"$mariadb_password\";/" /var/www/mysonet/db.php

# Créer un fichier temporaire contenant les commandes SQL
sql_commands=$(mktemp)
echo "use mysql;" >> "$sql_commands"
echo "ALTER USER 'root'@'localhost' IDENTIFIED BY '$mariadb_password';" >> "$sql_commands"
echo "create database mysonet;" >> "$sql_commands"
echo "use mysonet;" >> "$sql_commands"
echo "CREATE TABLE login (pseudo VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, token varchar(255) not null);" >> "$sql_commands"

# Demander le pseudo à l'utilisateur
read -p "Veuillez entrer un pseudo de connexion : " user_pseudo

# Demander le mot de passe à l'utilisateur et vérifier qu'il est saisi correctement deux fois
while true; do
    read -s -p "Veuillez entrer un mot de passe pour '$user_pseudo' : " user_password
    echo
    read -s -p "Veuillez confirmer le mot de passe : " user_password_confirm
    echo

    if [ "$user_password" = "$user_password_confirm" ]; then
        break
    else
        echo "Les mots de passe ne correspondent pas. Veuillez réessayer."
    fi
done

# Générer un token aléatoire de 32 caractères
user_token=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)

# Ajouter la commande SQL d'insertion au fichier temporaire
echo "INSERT INTO login (pseudo, mot_de_passe, token) VALUES ('$user_pseudo', PASSWORD('$user_password'), '$user_token');" >> "$sql_commands"

# Ajouter la commande SQL pour créer la table mes_postes
echo "CREATE TABLE mes_postes (ID INT AUTO_INCREMENT PRIMARY KEY, contenu TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP);" >> "$sql_commands"

# Ajouter la commande SQL pour créer la table mes_amis
echo "CREATE TABLE mes_amis (id INT AUTO_INCREMENT PRIMARY KEY, pseudo VARCHAR(255) NOT NULL, ip_add VARCHAR(40) NOT NULL, token VARCHAR(255) NOT NULL UNIQUE);" >> "$sql_commands"

# Ajouter la commande SQL pour créer la table demandes_recues
echo "CREATE TABLE demandes_recues (id INT NOT NULL AUTO_INCREMENT, demandeur VARCHAR(255) NOT NULL, ip_demandeur VARCHAR(40) NOT NULL, date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP, ref_demande VARCHAR(255) NOT NULL UNIQUE, statut VARCHAR(255) DEFAULT 'répondre', PRIMARY KEY (id));" >> "$sql_commands"

# Exécuter les commandes SQL
sudo mysql -u root < "$sql_commands"

# Supprimer le fichier temporaire
rm -f "$sql_commands"

# Suppression des variables contenant les mots de passe
unset mariadb_password mariadb_password_confirm

echo "Le mot de passe de l'utilisateur root dans MariaDB a été modifié avec succès."

### Installer certains paquets pour les connexions PHP à MariaDB
sudo apt -y install php8.1-mysql php8.1-curl

# Activer les modules PHP nécessaires
sudo phpenmod pdo_mysql
sudo phpenmod curl

# Redémarrer Apache pour appliquer les changements
sudo service apache2 restart

echo "Les paquets nécessaires pour les connexions PHP à MariaDB ont été installés et Apache a été redémarré avec succès."

### Donner les droits à www-data pour /var/www/mysonet
sudo chmod -R 755 /var/www/mysonet
sudo chown -R www-data:www-data /var/www/mysonet

### Donner les droits d'écriture au groupe pour les fichiers de demandes
sudo chmod g+w /home/inspectorsonet/demandes_en_attente
sudo chown :www-data /home/inspectorsonet/demandes_en_attente
sudo chmod g+w /home/inspectorsonet/demandes_envoyees
sudo chown :www-data /home/inspectorsonet/demandes_envoyees

echo "Les droits ont été correctement configurés."

### Permettre aux amis de récupérer nos postes en activant certains modules Apache
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod remoteip

### Redémarrer Apache pour appliquer les changements
sudo systemctl restart apache2

echo "Les modules Apache nécessaires ont été activés et Apache a été redémarré avec succès."

# Ajouter la tâche cron
(crontab -l; echo "*/15 * * * * /bin/bash $CURRENT_DIR/scripts/relance_reponse.sh") | crontab -

echo "La tâche cron pour automatiser l'envoi d'une réponse aux nouveaux amis a été configurée."

echo "1 - Votre serveur personnel MySoNet a été configuré."

echo "2 - Pour vous connectez à votre compte allez sur un navigateur web et taper votre IP ou nom de domaine."

echo "Pour trouvez des amis, connectez-vous sur mysonet.online !"
