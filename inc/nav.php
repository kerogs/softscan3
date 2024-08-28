<?php

// ? get url file name
$url = basename($_SERVER['PHP_SELF']);
?>

<nav>
    <ul>
        <a href="/">
            <li <?= $url == 'index.php' || $url == '' ? 'class="active"' : '' ?>><i class='bx bxs-home-heart'></i> Home</li>
        </a>
        <a href="/photos-gifs">
            <li><i class='bx bxs-image-alt'></i> Photos & GIFs</li>
        </a>
        <a href="/videos">
            <li><i class='bx bxs-video-recording'></i> Videos</li>
        </a>
        <a href="/all">
            <li <?= $url == 'all.php' ? 'class="active"' : '' ?> ><i class='bx bxs-collection'></i> All</li>
        </a>
        <a href="/collections">
            <li <li <?= $url == 'collections.php' ? 'class="active"' : '' ?> ><i class='bx bxs-collection'></i> Collections</li>
        </a>
    </ul>
</nav>