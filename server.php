<?php

require __DIR__ . '/vendor/autoload.php';

//Servidor WebSocket para Codeigniter 4
use Ratchet\Server\IoServer;      //Input Output
use Ratchet\Http\HttpServer;      //Protocolo de comunicación
use Ratchet\WebSocket\WsServer;   //Socket

use App\Libraries\Notify;         //Implementación socket

//Chat > Socket > Protocolo > Server(I/O)
$server = IoServer::factory(
  new HttpServer(
    new WsServer(
      new Notify()
    )
  ), 8080
);

echo "Servidor websocket iniciado en puerto 8080\n";
echo "Pulse CTRL + C para detener el servidor\n";

$server->run();