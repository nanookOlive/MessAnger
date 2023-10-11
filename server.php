<?php
session_start();

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Chat;


require_once __DIR__.'/vendor/autoload.php';

// create a loop(react), a socket(react) and return an instance of itself with the MessageComponentInterface(ratchet) cf class Chat
//by defauit ip address => 0.0.0.0 
// assigne value to attribute loop, socket and app (MessageComponentInterface)

$_SESSION['user']=[];

$server = IoServer::factory(

    new HttpServer(
        new WsServer(
            new Chat
            )
        ),
        8080
        );



$server->run(); // run $this->loop if loop != null




