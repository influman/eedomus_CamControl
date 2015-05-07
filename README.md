# eedomus_CamControl
Scripts de manipulation des caméras pour eedomus

NB : Script à installer sur serveur web/php autre que l'eedomus elle-même

CAMFTP
======

Script qui récupère une capture (snaphsot) et la transfere sur le FTP à la demande.

Dans ce fichier, il vous faut au préalable paramétrer :
- tous les liens de snapshot de vos caméras, avec IP:port distante ou ip:port locale en fonction d'où se trouve 
votre serveur de scripts php, et les user mots de passe respectifs
- le nombre de caméras
- tous les serveurs FTP. Vous pouvez en mettre d'autres que ceux d'eedomus si vous en avez des propres.
- le nombre de serveur FTP
- le serveur FTP centralisateur (votre serveur perso) : c'est celui par défaut lorsqu'on ne spécifie pas de serveur.

Plusieurs utilisations :
- Pour envoyer une capture de la caméra 1 vers son ftp eedomus 1 correspondant, l'appel est camftp.php?numcam=1&numftp1=1
- Pour envoyer une capture de la caméra 1 vers son ftp eedomus 1 correspondant 
ainsi que sur un autre ftp propre en même temps (le 3), l'appel est : camftp.php?numcam=1&numftp1=1&numftp2=3
- Pour envoyer une capture de toutes les caméras vers le ftp centralisateur, 
l'appel est : camftp.php sans argument ou camftp.php?numcam=99

ATTENTION
Si vous voulez plusieurs captures d'un coup (lors d'une intrusion par exemple), je conseille de le gérer via des appels 
successifs au script via une macro eedomus.
Cependant, le script le gère en rajoutant les paramètres "nbsnap" (nombres de captures) et 
"updelay" (intervalles en secondes entre deux captures). 
Le problème c'est que le script dure le temps de nbsnap x updelay secondes avant de se terminer.

PRE-REQUIS
Sur le serveur PHP, il y a souvent un blocage par défaut de l'envoi des données vers le FTP destinataire. 
Dans ce cas, la connexion passe, ainsi que le login, mais pas le transfert : erreur "Can't build data connection". 
Le fichier de destination est alors vide.
Sur le NAS Synology qui traite ce script, j'ai réglé ce blocage en laissant passer le port 20 (en tant que source) 
sur le firewall.

Pour s'affranchir de ce script, peut-être que l'équipe eedomus pourrait nous intégrer un moyen d'automatiser 
le transfert vers le ftp respectif des caméras, via un état canal complémentaire utilisable en macro et règles.

edit 1 : Ajout envoi de mail

Pré-requis :
Dans le fichier php, il faut paramétrer le mail destinataire (gmail).
Côté serveur php, il faut paramétrer le serveur smtp. Sur mon Nas Synology, j'ai testé avec succès en associant 
mon compte gmail (procédure intégrée de google) comme smtp dans Configuration>Notification>Email 
et un destinataire gmail.com. Le code ne fonctionne pas pour d'autres client/serveur que gmail pour le moment...
Avant d'écraser votre ancien fichier php, pensez à copier vos paramètres existants de caméras et ftp.

Utilisation :
Envoi de la caméra 1 par mail sans ftp : camftp.php?numcam=1&getmail=1 
Envoi de la caméra 1 par mail et ftp : camftp.php?numcam=1&numftp1=1&getmail=1
Envoi de toutes les caméras dans un seul mail avec envoi au serveur ftp centralisateur : camftp.php?getmail=1

En cumulant FTP + Mail, il y aura sans doute 1s d'écart dans les images transmises entre le FTP et le MAIL

======
IMGFTP
======
Permet de restituer dans un widget "contenue HTML" d'eedomus les images issues de serveurs FTP.
Le Script permet de restituer de 1 à 4 dernières captures dans le même widget.
En précisant dans l'URL la variable numimg=4 par exemple.

Contenu HTML eedomus : remplacer les "!" par des "<"

!html>!head></head>!body>!P style="text-align:center">!img width=285 height=217 
src="http://xx.xx.xx.xx/imgftp.php?numimg=4"!/img>!/p>!/body>!/html>

Dans le script,
Vous pouvez préciser plusieurs ftp (si applicable) et leurs paramètres d'accès associés.

Deux paramètres possibles dans l'url d'appel donc : imgftp.php?numftp=1&prefix=Camera_Avant
- numftp, pour préciser sur quel serveur ftp on cherche
- prefix, pour préciser le préfixe à sélectionner dans le nom de la capture
si on ne précise rien, ça restitue la dernière capture du premier ftp paramétré.

je n'ai pas poussé le test sur les serveurs ftp d'eedomus, a priori ça ne marche pas..

Illustration ici : http://forum.eedomus.com/viewtopic.php?f=25&t=2613&p=24698&hilit=imgftp#p24698
