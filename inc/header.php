<header>
    <div class="title"><a href="/">Soft<span>Scan</span><span>3</span></a></div>
    <div class="search">
        <form action="/search" method="get">
            <label for="search">
                <input value="<?= basename($_SERVER['PHP_SELF']) == 'search.php' && $_GET['type'] ? $_GET['search'] : ''; ?>" type="text" id="search" name="search" placeholder="Search...">
                <select name="type" id="">
                    <option <?= $_GET['type'] == 'all' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : ''; ?> value="all">All</option>
                    <option <?= $_GET['type'] == 'image' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : ''; ?> value="image">Image</option>
                    <option <?= $_GET['type'] == 'video' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : ''; ?> value="video">Video</option>
                    <option <?= $_GET['type'] == 'gif' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : ''; ?> value="gif">GIF</option>
                    <option <?= $_GET['type'] == 'other' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : ''; ?> value="other">Image/GIF</option>
                </select>
                <button type="submit"><i class='bx bx-search'></i></button>
            </label>
        </form>
    </div>
    <div class="btnplus">
        <button onclick="configurationPopup.classList.toggle('active')"><i class='bx bx-cog'></i> <span>Configuration</span></button>
        <button onclick="informationPopup.classList.toggle('active')"><i class='bx bx-info-circle'></i> <span>Information</span></button>
    </div>
    <div class="githubad">
        <div class="card">
            <h2>SS3 Github</h2>
            <a href="https://github.com/kerogs/softscan3" target="_blank"></a>
            <img src="./src/img/ssg/bannergithub.png" alt="">
        </div>
    </div>
</header>

<div id="configurationPopup" class="popup">
    <div class="content">
        <div class="titlee">
            <h2>Configuration</h2>
            <i class='bx bx-x' onclick="configurationPopup.classList.toggle('active')"></i>
        </div>
        <div class="titlee">
            <h3>Configuration du serveur</h3>
            <i class='bx bxs-cog'></i>
        </div>
        <?php
        if (
            file_exists(__DIR__ . '/../dist/ffmpeg/bin/ffmpeg.exe') &&
            file_exists(__DIR__ . '/../dist/ffmpeg/bin/ffprobe.exe') &&
            file_exists(__DIR__ . '/../dist/ffmpeg/bin/ffplay.exe')
        ) :
        ?>
            <div>
                <?php if ($srvConfigJSON['autoFFMPEG']) : ?>
                    <a href="/action/autoFFMPEG.php?set=false"><button class="nnhover <?= $srvConfigJSON['autoFFMPEG'] ? 'active' : '' ?>"><span>FFMPEG auto activé</span><span>Désactiver le FFMPEG auto</span></button></a>
                <?php else : ?>
                    <a href="/action/autoFFMPEG.php?set=true"><button class="nnhover <?= $srvConfigJSON['autoFFMPEG'] ? 'active' : '' ?>"><span>FFMPEG auto désactivé</span><span>Activer le FFMPEG auto</span></button></a>
                <?php endif; ?>
            </div>
            <button onclick="ffmpegReload()">Forcer le rechargement FFMPEG</button>
        <?php else: ?>
            <p class="alertMessageFFMPEG">
                FFMPEG n'est pas installé sur le serveur. Vous avez besoins de se dernier pour afficher les préviews des vidéos. <a href="https://github.com/kerogs/softscan3?tab=readme-ov-file#installation" target="_blank">En savoir plus </a>
            </p>
        <?php endif; ?>
        <div class="splitConfigForm">
            <input type="text" name="" placeholder="New password" id="newPasswordInput">
            <input type="submit" id="newPasswordBtn" onclick="newPassword()" value="Save">
        </div>
        <div class="titlee">
            <h3>Sécurité</h3>
            <i class='bx bxs-shield-alt-2'></i>
        </div>
        <div>
            <?php if ($srvConfigJSON['allow'] == "INTRANET") : ?>
                <a href="/action/allowFilter.php?allow=LOCAL"><button>FILTRE IP : INTRANET</button></a>
            <?php elseif ($srvConfigJSON['allow'] == "LOCAL") : ?>
                <a href="/action/allowFilter.php?allow=ALL"><button>FILTRE IP : LOCAL</button></a>
            <?php else : ?>
                <a href="/action/allowFilter.php?allow=INTRANET"><button>FILTRE IP : ALL</button></a>
            <?php endif; ?>
        </div>
        <div class="dangerzone">
            <div class="titlee">
                <h3>Zone de danger</h3>
                <i class='bx bxs-error'></i>
            </div>
            <button onclick="logsreset()">Logs reset</button>
            <button onclick="nukeData()">Nuke data</button>
            <button onclick="stopServer()">Eteindre le serveur</button>
        </div>
    </div>
</div>

<script>
    function newPassword() {
        swal.fire({
            // toast:true,
            title: "Voulez-vous changez le mot de passe ?",
            text: "Tous les utilisateurs connectés seront déconnectés.",
            icon: "question",
            confirmButtonText: "Oui",
            cancelButtonText: "Non",
            reverseButtons: true,
            showCancelButton: true,
        }).then((verif) => {
            if (verif.isConfirmed) {
                let newPassword = document.getElementById('newPasswordInput').value;
                let newPasswordBtn = document.getElementById('newPasswordBtn');
                const bodyArea = document.querySelector("body");

                if (newPassword == '') {
                    swal.fire({
                        icon: 'error',
                        text: 'Vous devez entrer un mot de passe.',
                    })
                } else {

                    swal.fire({
                        // loading
                        title: 'action en cours',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        didOpen: () => {
                            swal.showLoading()
                        }
                    });

                    fetch('action/newPassword.php?newPassword=' + newPassword)
                        .then(response => response.text())
                        .then(data => {

                            // data to JSON
                            var data = JSON.parse(data);

                            // check if data.success is true

                            if (data.success) {
                                swal.fire({
                                    icon: 'success',
                                    title: 'Operation terminée',
                                    text: data.message,
                                })
                            } else {
                                swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: data.message,
                                })
                            }
                        })
                        .catch(error => {
                            console.error(error)

                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Une erreur est survenue.',
                            })
                        });


                }
            }
        })
    }

    function stopServer() {
        swal.fire({
            // toast:true,
            title: "Voulez-vous éteindre le serveur ?",
            text: "Une commande sera utilisé sur le serveur.",
            icon: "question",
            confirmButtonText: "Oui",
            cancelButtonText: "Non",
            reverseButtons: true,
            showCancelButton: true,
        }).then((verif) => {
            if (verif.isConfirmed) {

                swal.fire({
                    // loading
                    title: 'action en cours',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading()
                    }
                });

                fetch('action/stopServer.php')
                    .then(response => response.text())
                    .then(data => {

                        // data to JSON
                        var data = JSON.parse(data);

                        // check if data.success is true

                        if (data.success) {
                            swal.fire({
                                icon: 'success',
                                title: 'Operation terminée',
                                text: data.message,
                            })
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            })
                        }
                    })
                    .catch(error => {
                        console.error(error)

                        swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Une erreur est survenue.',
                        })
                    });
            }
        });
    }

    function nukeData() {
        swal.fire({
            // toast:true,
            title: "Supprimer toutes les données",
            text: "Les images, vidéos et thumbnails seront supprimées.",
            icon: "question",
            confirmButtonText: "Oui",
            cancelButtonText: "Non",
            reverseButtons: true,
            showCancelButton: true,
        }).then((verif) => {
            if (verif.isConfirmed) {

                swal.fire({
                    // loading
                    title: 'action en cours',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading()
                    }
                });

                fetch('action/nukedata.php')
                    .then(response => response.text())
                    .then(data => {

                        // data to JSON
                        var data = JSON.parse(data);

                        // check if data.success is true

                        if (data.success) {
                            swal.fire({
                                icon: 'success',
                                title: 'Operation terminée',
                                text: data.message,
                            })
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            })
                        }
                    })
                    .catch(error => {
                        console.error(error)

                        swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Une erreur est survenue.',
                        })
                    });
            }
        });
    }

    function logsreset() {
        swal.fire({
            // toast:true,
            title: "Souhaitez-vous reset les logs",
            text: "Le fichier des logs sera vidé complètement.",
            icon: "question",
            confirmButtonText: "Oui",
            cancelButtonText: "Non",
            reverseButtons: true,
            showCancelButton: true,
        }).then((verif) => {
            if (verif.isConfirmed) {

                swal.fire({
                    // loading
                    title: 'action en cours',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        swal.showLoading()
                    }
                });

                fetch('action/logsreset.php')
                    .then(response => response.text())
                    .then(data => {

                        // data to JSON
                        var data = JSON.parse(data);

                        // check if data.success is true

                        if (data.success) {
                            swal.fire({
                                icon: 'success',
                                title: 'Operation terminée',
                                text: data.message,
                            })
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                            })
                        }
                    })
                    .catch(error => {
                        console.error(error)

                        swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Une erreur est survenue.',
                        })
                    });
            }
        });
    }

    function ffmpegReload() {
        // const bodyArea = document.querySelector("body");
        // bodyArea.innerHTML += '<div class="littlePopup notif"><p>Rechargement FFMPEG en cours. Le serveur risque de lagger un certain temps.</p></div>';

        swal.fire({
            // toast:true,
            title: "Forcer le rechargement FFMPEG",
            text: "Le rechargement FFMPEG peut faire laguer le serveur plus ou moins longtemps en fonction de la puissance du serveur et du nombre de contenu enregistré. Cela va scanner toutes les images et vidéos sur le serveur afin de créer des thumbnails.",
            icon: "question",
            confirmButtonText: "Oui",
            cancelButtonText: "Non",
            reverseButtons: true,
            showCancelButton: true,
        }).then((verif) => {
            if (verif.isConfirmed) {

                xhr = new XMLHttpRequest();
                xhr.open("GET", "action/ffmpegReload.php", true);
                xhr.send();

                swal.fire({
                    icon: 'success',
                    title: 'Opération en cours... Le serveur va lagger pendant un certain temps.',
                    text: data.message,
                })
            }
        });
    }
</script>

<div id="informationPopup" class="popup">
    <div class="content">
        <div class="titlee">
            <h2>Information</h2>
            <i class='bx bx-x' onclick="informationPopup.classList.toggle('active')"></i>
        </div>
        <ul class="infopopup">
            <li><i class='bx bxs-window-alt'></i> Site version - <?= $kpf_config["other"]["website_version"] ?></li>
            <!-- <li><i class='bx bxs-flag-checkered'></i> Site github version - <?= $kpfss3_version_github[1] ?></li> -->
            <li><i class='bx bxs-extension'></i> Framework version - <?= $kpf_config["framework"]["framework_version"] ?> (<?= $kpf_config["framework"]["title_short"] ?>)</li>
            <!-- <li><i class='bx bxs-flag-checkered'></i> Framework github version - <?= $kpfss3_version_github[0] ?></li> -->
            <!-- <li><i class='bx bxs-time'></i> Dernière vérification - <?= $kpfss3_version_github[2] ?></li> -->
        </ul>
    </div>
</div>