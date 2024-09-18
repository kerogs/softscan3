<div align="center">
    <img alt="Logo" src=".ksinf/icon_prev.png" height="100">
    <h3>SoftScan3</h3>
    <p><em>v3 de SoftScan, SoftScan est un scanneur d'image, vidéo et gif pour un dossier spécifique. Il permet de gérer facilement une galerie d'images sans configuration nécessaire.</em></p>
</div>

<div align="center">
<img alt="" src=".ksinf/banner_low.png">
</div>

## Features
- afficher les images de chaque dossier et sous-dossier
- proposer des images en rapport avec celle sélectionné
- tous les formats possible [jpg/jpeg, png, svg, gif, webp, webm, mov, avi, mp4, ...]
- responsive tout format
- design agréable
- afficher les derniers contenu vu
- afficher les derniers contenu ajouté
- afficher les dernières collections modifié
- Galerie complète pour vidéo
- Galerie complète pour image/gif
- Galerie complète de tout
- système de vues, like, dislike
- bouton "Surprends moi"
- Création automatique de thumbnails pour les vidéos via FFMPEG
- Possibilité d'ajout de dossier complet directement sur le site (dossier non zippé)
- Possibilité d'ajouter et supprimer des dossiers ET/OU de créer des dossiers depuis la page web.
- Possibilité de nuke les data depuis le site
- Possibilité de clear les logs depuis le site
- Possibilité de forcer le chargement FFMPEG depuis le site
- Possibilité de d'éteindre le serveur à distance

- fonctionnalité de recherche avancé (fonctionne avec des url de dossiers, des extensions différentes, ect...)


### Preview
#### Ordinateur
<div align="center">
    <img alt="" src=".ksinf/prevpc1.png" width="32%">
    <img alt="" src=".ksinf/prevpc2.png" width="33%">
    <img alt="" src=".ksinf/prevpc3.png" width="33%">
</div>

#### Téléphone
<div align="center">
    <img alt="" src=".ksinf/prevph1.jpg" width="32%">
    <img alt="" src=".ksinf/prevph2.jpg" width="33%">
    <img alt="" src=".ksinf/prevph3.jpg" width="33%">
</div>

## A savoir
- Le renouvellement des thumbnails s'effectue toutes les 6 minutes (+ page d'accueil)
- le stockage des stats se fait dans ``/public/temp/stats.json``

## Installation
1. Installer tout le repository
```sh
git clone https://github.com/kerogs/softscan3.git
```
2. Le mettre sur un serveur local

3. Installer les packages composer
```sh
composer i
```

4. Installer les packages NPM
```sh
cd public; npm i
```

5. Installer le programme **FFMPEG** pour les thumbnails
    1. Installer le ``.7z`` sur le site officiel ([cliquer ici](https://www.ffmpeg.org/download.html))

    2. Décompresser le dossier

    3. Dans le dossier décompresser récupérer les 3 fichiers suivant et les déposer dans ``/dist/ffmpeg/bin/``
        - ``chemin/vers/dossier/ffmpeg/bin/ffmpeg.exe``
        - ``chemin/vers/dossier/ffmpeg/bin/ffprobe.exe``
        - ``chemin/vers/dossier/ffmpeg/bin/ffplay.exe``

    4. Vous avez donc normalement sur le site l'arborescence suivante pour le dossier ``dist`` :

    ```sh
    📦dist
     ┣ 📂ffmpeg
     ┃ ┗ 📂bin
     ┃ ┃ ┣ 📜.gitkeep
     ┃ ┃ ┣ 📜ffmpeg.exe
     ┃ ┃ ┣ 📜ffplay.exe
     ┃ ┃ ┗ 📜ffprobe.exe
     ┗ 📜last_ffmpeg.ksc
    ```

6. Déposer les images dans ``/public/public_data/`` ou les importer directement depuis le site


## php.ini
> [!IMPORTANT]
> Si vous souhaitez autoriser l'envoi de fichier depuis le site, il est vivement recommendé de changer la configuration du fichier php.ini
1. Aller dans le fichier php.ini correspondant à votre version de PHP. Avec MAMP et la version PHP 8.3.1 alors ce sera : ``C:\MAMP\conf\php8.3.1\php.ini``
1. Configurer les valeurs suivante (cette configuration est un example mais fonctionne très bien pour du local):
```ini
upload_max_filesize = 9999999G
post_max_size = 99999999G
max_file_uploads = 500000
memory_limit = 999999G
max_input_time = 360000
max_execution_time = 360000
``` 
1. Redémarrer votre serveur web.
1. C'est fait.



## A ajouter
- [x] Paramètre
- [x] Infobox
- [x] Pouvoir ajouter des images/video/gif en plusieurs fois
- [x] capable de faire un tris dans le contenu
- [x] possibilité d'éteindre le serveur à distance
- [x] faire un nuke du site
- [x] faire le design
- [x] faire le responsive
- [ ] faire une page soutien (redirection vers KerogsPHP Framework ou encore l'url du github de SoftScan3)
- [x] Créer une mascotte (future icone du site et pour les boutons ect).
- [x] faire les détourages dela mascotte
- [x] faire une page login
- [x] faire un système de logs du site
- [x] supprimer des dossiers/sous_dossier
- [x] proposer les différents dossier
- [ ] cacher les images dislike