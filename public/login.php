<?php

session_start();

const KEY_ACCESS = "alexia1";

if (isset($_POST['password'])) {

    $password = htmlentities($_POST['password']);

    if ($password == KEY_ACCESS) {

        $_SESSION['keyaccess'] = KEY_ACCESS;
        header("location: ./");
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./src/css/login.css">
</head>

<body>

    <div class="ccenter">
        <h1>Login</h1>
        <form action="" method="post">
            <label for="password">Clé de connexion</label>
            <input type="password" name="password" placeholder="Password">
            <input type="submit" value="Login">
        </form>
    </div>

</body>

</html>