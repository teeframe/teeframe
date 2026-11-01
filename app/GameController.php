<?php

namespace App;

use TeeFrame\Core\TickHandler;
use TeeFrame\Game\PvpGameController;

class GameController extends PvpGameController
{
    public function __construct(
        TickHandler $tickHandler,
    ) {
        parent::__construct(
            tickHandler: $tickHandler,
            isTeamMode: false,
            scoreLimit: 5,
        );
    }

    public function setGameMode(string $gameMode): void
    {
        parent::setGameMode($gameMode);

        $this->scoreLimit = match ($this->getGameType()) {
            'CTF' => 300,
            'TDM' => 20,
            default => 5,
        };
    }
}
