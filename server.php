<?php

use Ratchet\Server\IoServer;

use App\Chat;


require_once __DIR__.'/vendor/autoload.php';
$server = IoServer::factory(new Chat,8080);
$server->run();




