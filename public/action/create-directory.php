<?php
    $path = $_POST['dir'];
    $path = trim($path);
    $name = $_POST['name'];

    if(mkdir("../".$path."/".$name)){
        header('Location: ../add/create?success=ok');
    } else{
        header('Location: ../add/create?success=ko');
    }

    // echo "../".$path.'/'.$name;
