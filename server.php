<?php

require __DIR__ . '/vendor/autoload.php';

use App\World;

use TeeFrame\Core\TickHandler;
use TeeFrame\Game\AbstractWorld;
use TeeFrame\Server\AbstractServerInstance;
use TeeFrame\Server\Config\WorldConfig;

$server = new class(
    tickHandler: new TickHandler,
    config: __DIR__.'/config.cfg',
    activatePvpSettings: true,
) extends AbstractServerInstance
{
    protected function createWorld(WorldConfig $config): AbstractWorld
    {
        return new World($config, $this->tickHandler, $this);
    }
};

$server->start();
