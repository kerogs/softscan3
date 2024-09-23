<?php
// KerogsPHP Framework
// https://github.com/KSInfinite/KerogsPHP-Framework
// Thanks for using it.

// ! Users cannot access this root file/folder. For them, the root file will be in /public/.

// Frontend : /public/
// Backend : /backend/
// Labs/Test : /test/
// Include : /inc/
// Error : /public/error/
// Docs : /public/docs/

// This file will call up everything you need for each of your pages. This is the file to call.
// ? You can call this file with this command : require_once('../config.php'); 




// ======================================> Configuration php
// Path base for the project
$path = __DIR__;
// Import the core php file
require_once($path . '/backend/core.php');
// Import composer
if (file_exists($path . '/vendor/autoload.php')) require_once($path . '/vendor/autoload.php');
// ======================================>

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') $kpf_urlHTTP = "https://";
else $kpf_urlHTTP = "http://";
$kpf_urlHOST = $_SERVER['HTTP_HOST'];

// Show PHP info (uncomment the line below)
// echo phpinfo();


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

    if(prependToFile($pathLogs, $contentToAdd)){
        return true;
    } else{
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








// ! FFMPEG
$lastFfmpegFile = $path . '/dist/last_ffmpeg.ksc';
$logFile = $path . '/server.log'; // Chemin vers le fichier de logs

// Vérifier si le fichier last_ffmpeg.ksc existe
if (
    file_exists($lastFfmpegFile) && "index.php" == basename($_SERVER['PHP_SELF'])
) {

    $fileModificationTime = filemtime($lastFfmpegFile);
    $currentTime = time();
    $sixMinutes = 12 * 60; // 6 minutes en secondes

    if (($currentTime - $fileModificationTime) > $sixMinutes) {
        // Appeler le script ffmpeg_start.php en arrière-plan
        // exec('php ' . escapeshellarg($path . '/ffmpeg_start.php') . ' > /dev/null 2>&1 &');
        require 'ffmpeg_start.php';

        // Mise à jour de la date de modification du fichier last_ffmpeg.ksc
        $oldDate = date("Y-m-d H:i:s", $fileModificationTime);
        touch($lastFfmpegFile);
        $newDate = date("Y-m-d H:i:s");

        // Écrire dans les logs
        logs($logFile, "FFMPEG start script called. File modified date was $oldDate and updated to $newDate.", 200, "INFO");
    } else {
        // Pas besoin de mettre à jour les thumbnails
        logs($logFile, "No need to update thumbnails. The file was modified less than 6 minutes ago.", 200, "INFO");
    }
} else {
    // Fichier last_ffmpeg.ksc n'existe pas
    if(!file_exists($lastFfmpegFile)) {
        // Creer le fichier last_ffmpeg.ksc
        logs($logFile, "The file last_ffmpeg.ksc does not exist.", 500, "ERROR");
    }
}








// config.yml
// ! Don't touch everything under this line.
use Symfony\Component\Yaml\Yaml;

$kpf_configFilePath = $path . '/config.yml';
$kpf_config = Yaml::parseFile($kpf_configFilePath);


// ! check version framework KPF
use Kerogs\KerogsPhp\Github;
$kp_github = new Github();
$kp_github_lastversion = $kp_github->getLatestRelease('KSLaboratories', 'KerogsPHP-Framework');