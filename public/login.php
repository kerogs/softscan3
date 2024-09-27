<?php

// ! Logs maker (see /server.log)
/**
 * Logs a message with various details to a specified file.
 *
 * @param string $pathLogs The path to the log file.
 * @param string $message The message to be logged. Defaults to "-".
 * @param int $statusCode The HTTP status code. Defaults to 200.
 * @param string $logType The type of log. Defaults to "INFO".
 * @return void
 */
function logs($pathLogs, $message = "-", $statusCode = 200, $logType = "INFO")
{

    $uniqid = uniqid();
    $timestamp = date("Y-m-d H:i:s.u");
    $ipv4 = isValidIpAddress($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "-";

    $httpMethod = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'HTTPS' : 'HTTP';

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://';
    $pathShow = isset($_SERVER['REQUEST_URI']) ? $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] : "-";
    $pathShowWithoutQuery = isset($_SERVER['REQUEST_URI']) ? $protocol . $_SERVER['HTTP_HOST'] . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : "-";
    $pathReal = isset($_SERVER['SCRIPT_NAME']) ? $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] : "-";
    if ($pathShowWithoutQuery === $pathReal) {
        $pathReal = "-";
    }

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $requestMethod = "GET";
            // $_GET peut être vide même si la méthode est GET
            $data = !empty($_GET) ? $_GET : '-';
            break;

        case 'POST':
            $requestMethod = "POST";
            // $_POST peut également être vide même si la méthode est POST
            $data = !empty($_POST) ? $_POST : '-';
            break;

        case 'PUT':
            $requestMethod = "PUT";
            // Récupérer les données de la requête PUT
            $putData = file_get_contents('php://input');
            parse_str($putData, $data);
            $data = !empty($data) ? $data : '-';
            break;

        case 'DELETE':
            $requestMethod = "DELETE";
            // Récupérer les données de la requête DELETE
            $deleteData = file_get_contents('php://input');
            parse_str($deleteData, $data);
            $data = !empty($data) ? $data : '-';
            break;

        default:
            $requestMethod = "-";
            $data = "-";
            break;
    }

    if ($data != "-") {
        $response = [
            'data' => $data
        ];

        $responseOutput = json_encode($response);
    } else {
        $responseOutput = "-";
    }

    $contentToAdd = "[$statusCode] [$uniqid] $timestamp $ipv4 [$logType] $message [$httpMethod] [$pathShow] ($pathReal) $requestMethod $responseOutput";

    if (prependToFile($pathLogs, $contentToAdd)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if the given IP address is valid.
 *
 * @param string $ip The IP address to validate.
 * @return bool Returns true if the IP address is valid, false otherwise.
 */
function isValidIpAddress($ip)
{
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
        return true;
    }
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
        return true;
    }
    return false;
}

/**
 * Prepends new content to the beginning of a file.
 *
 * @param string $filePath The path to the file.
 * @param string $newContent The content to be prepended.
 * @return bool Returns true if the content was successfully prepended, false otherwise.
 */
function prependToFile($filePath, $newContent)
{
    if (!file_exists($filePath)) {
        return false;
    }
    $currentContent = file_get_contents($filePath);
    $file = fopen($filePath, 'w');
    if ($file === false) {
        return false;
    }
    fwrite($file, $newContent . PHP_EOL);
    fwrite($file, $currentContent);
    fclose($file);
    return true;
}



// ! default section


session_start();

const KEY_ACCESS = "kerogs";

if (isset($_POST['password'])) {

    $password = htmlentities($_POST['password']);

    if ($password == KEY_ACCESS) {

        $_SESSION['keyaccess'] = KEY_ACCESS;
        logs("../../server.log", "Connecté", 200, "ACCEPT");
        header("location: ./");
        exit();
    } else {
        logs("../../server.log", "Mot de passe incorrect", 401, "REJECT");
    }
} else {
    logs("../server.log", "Nouvelle connexion détecté", 200, "INFO");
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

<style>
    .topAnimationLoad {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;

        /* background-color: #fff; */
    }

    .topAnimationLoad #headertohundred {
        width: 0%;
        height: 3px;
        background-color: #a038ec;
    }
</style>

<body>

    <div class="topAnimationLoad">
        <div id="headertohundred"></div>
    </div>

    <div class="ccenter">
        <h1>Login</h1>
        <form action="" method="post">
            <label for="password">Clé de connexion</label>
            <input type="password" name="password" placeholder="Password">
            <input type="submit" id="buttonlogin" onclick="loginAnimation();             document.getElementById('buttonlogin').value = 'Connexion en cours...';" value="Login">
        </form>
    </div>

    <script>
        loginAnimation = () => {
            headertohundred = document.getElementById('headertohundred');

            for (let i = 0; i <= 100; i++) {
                setTimeout(() => {
                    headertohundred.style.width = i + '%';
                }, i * 16);
            }
        }
    </script>

</body>

</html>