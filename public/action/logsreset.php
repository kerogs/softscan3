<?php
    require_once '../../config.php';

    unlink('../../server.log');

    file_put_contents("../../server.log", "");

    logs('../../server.log', "LOGS NUKE (action from user)", 200, "NUKE");
