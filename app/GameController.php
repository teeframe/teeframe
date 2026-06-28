<?php

namespace App;

use TeeFrame\Core\TickHandler;
use TeeFrame\Game\AbstractGameController;

class GameController extends AbstractGameController
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
}
