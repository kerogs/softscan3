<?php

require_once('../../config.php');

if(logs('../../server.log', "Server going to stop (action from user)", 410, "ALERT")){
    logs('../../server.log', "Server stopped", 200, "INFO");

    // php exec to stop the server
    shell_exec('shutdown /s /f /t 5');
}

?>