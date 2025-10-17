<?php

namespace App\Libraries;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class NotifySocket implements MessageComponentInterface
{
    protected $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "Nueva conexión de cliente: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Esperamos un mensaje JSON tipo {type: "new_averia", data: {...}}
        $data = json_decode($msg, true);

        if(isset($data['type']) && $data['type'] === 'new_averia'){
            $this->broadcast([
                'type' => 'new_averia',
                'averia' => $data['averia']
            ]);
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
        echo "Conexión {$conn->resourceId} desconectada\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function broadcast($data)
    {
        $msg = json_encode($data);
        foreach($this->clients as $client){
            $client->send($msg);
        }
    }
}
