<header>
    <div class="title"><a href="/">Soft<span>Scan</span><span>3</span></a></div>
    <div class="search">
        <form action="/search" method="get">
            <label for="search">
                <input value="<?= basename($_SERVER['PHP_SELF']) == 'search.php' && $_GET['type'] ? $_GET['search'] : '' ; ?>" type="text" id="search" name="search" placeholder="Search...">
                <select name="type" id="">
                    <option <?= $_GET['type'] == 'all' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : '' ; ?> value="all">All</option>
                    <option <?= $_GET['type'] == 'image' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : '' ; ?> value="image">Image</option>
                    <option <?= $_GET['type'] == 'video' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : '' ; ?> value="video">Video</option>
                    <option <?= $_GET['type'] == 'gif' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : '' ; ?> value="gif">GIF</option>
                    <option <?= $_GET['type'] == 'other' && basename($_SERVER['PHP_SELF']) == 'search.php' ? 'selected' : '' ; ?> value="other">Image/GIF</option>
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
        <button onclick="ffmpegReload()">Forcer le rechargement FFMPEG</button>
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
    function stopServer() {

        // ? add popup message
        const askConfirmation = confirm("Êtes-vous sur de vouloir éteindre le serveur ?");

        if (askConfirmation) {
            const bodyArea = document.querySelector("body");
            bodyArea.innerHTML += '<div class="littlePopup notif"><p>Le serveur est entrain de s\'arrêter.</p></div>';

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "action/stopServer.php", true);
            xhr.send();
        }
    }

    function nukeData() {
        // ? add popup message
        const askConfirmationNuke = confirm("Êtes-vous sur de vouloir nuke les datas (image comprise) ?");

        if (askConfirmationNuke) {
            const bodyArea = document.querySelector("body");
            bodyArea.innerHTML += '<div class="littlePopup notif"><p>Les données sont entrain d\'etre supprimées.</p></div>';

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "action/nukedata.php", true);
            xhr.send();
        }
    }

    function logsreset() {
        // ? add popup message
        const askConfirmationLogsreset = confirm("Êtes-vous sur de vouloir reset les logs ?");

        if (askConfirmationLogsreset) {
            const bodyArea = document.querySelector("body");
            bodyArea.innerHTML += '<div class="littlePopup notif"><p>Les logs sont entrain d\'etre reset.</p></div>';

            var xhr = new XMLHttpRequest();
            xhr.open("GET", "action/logsreset.php", true);
            xhr.send();
        }
    }

    function ffmpegReload() {
        const bodyArea = document.querySelector("body");
        bodyArea.innerHTML += '<div class="littlePopup notif"><p>Rechargement FFMPEG en cours. Le serveur risque de lagger un certain temps.</p></div>';

        var xhr = new XMLHttpRequest();
        xhr.open("GET", "action/ffmpegReload.php", true);
        xhr.send();
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
            <li><i class='bx bxs-flag-checkered' ></i> Site github version - <?= $kpfss3_version_github[1] ?></li>
            <li><i class='bx bxs-extension'></i> Framework version - <?= $kpf_config["framework"]["framework_version"] ?> (<?= $kpf_config["framework"]["title_short"] ?>)</li>
            <li><i class='bx bxs-flag-checkered' ></i> Framework github version - <?= $kpfss3_version_github[0] ?></li>
            <li><i class='bx bxs-time'></i> Dernière vérification - <?= $kpfss3_version_github[2] ?></li>
        </ul>
    </div>
</div>