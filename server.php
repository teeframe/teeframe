<?php

require __DIR__ . '/vendor/autoload.php';

use App\World;

use TeeFrame\Core\TickHandler;
use TeeFrame\Game\AbstractWorld;
use TeeFrame\Server\AbstractServerInstance;
use TeeFrame\Server\Sockets\UdpSocket;

class GameServer extends AbstractServerInstance
{
    protected function boot(): void
    {
        $this->worlds[] = new World('dm1', $this->tickHandler, $this);

        $this->sockets[] = new UdpSocket('0.0.0.0', 8304);
    }

    protected function selectWorldForNewConnection(): AbstractWorld
    {
        return $this->worlds[0];
    }
}

$server = new GameServer(new TickHandler);

$server->start();
