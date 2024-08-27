<?php

    session_start();

    // ? Import functions
    require_once __DIR__."/func/functions.php";
    // ? import class
    require_once __DIR__."/class/class.php";
    
    function genFakeType() {
        $fakeCategory = array(
            array(
                "name" => "Video",
                "icon" => "<i class='bx bxs-video-recording'></i>"
            ),
            array(
                "name" => "GIF",
                "icon" => "<i class='bx bxs-image-alt'></i>"
            ),
            array(
                "name" => "Photo",
                "icon" => "<i class='bx bxs-image-alt'></i>"
            ),
        );

        $randomKey = array_rand($fakeCategory); 
        $randomCategory = $fakeCategory[$randomKey];

        return $randomCategory;
    }

    function genFakeCategory() {
        $fakeCategory = array("Anime", "Animation", "Reddit", "Tendance", "jstn", "alsn", "supervideo", "fun", "manga", "manhwa", "manhua");
        return $fakeCategory[array_rand($fakeCategory)];
    }