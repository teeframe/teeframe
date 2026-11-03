<?php

namespace App;

use App\Commands\Server\LockTeamsCommand;
use App\Commands\Server\RestartCommand;
use App\Commands\Server\SetTeamAllCommand;
use App\Commands\Server\SetTeamCommand;
use App\Commands\Server\ShuffleTeamsCommand;
use App\Commands\Server\SwapTeamsCommand;
use TeeFrame\Core\TickHandler;
use TeeFrame\Game\AbstractWorld;
use TeeFrame\Game\Entities\Character\AbstractCharacterEntity;
use TeeFrame\Game\Entities\Character\PvpCharacterEntity;

use TeeFrame\Game\World\PickupSpawner;
use TeeFrame\Game\World\Vector2;
use TeeFrame\Map\Map;
use TeeFrame\Map\MapLayers\GameLayer;
use TeeFrame\Server\AbstractServerInstance;
use TeeFrame\Server\Config\WorldConfig;

class World extends AbstractWorld
{
    protected GameController $dmController;

    public function __construct(
        WorldConfig $config,
        TickHandler $tickHandler,
        AbstractServerInstance $server,
    ) {
        parent::__construct(
            $config->identifier,
            $tickHandler,
            new Map(__DIR__.'/../maps/'.$config->settings->getString('sv_world_map').'.map'),
            $server,
            slotsLimit: $config->slotsLimit,
            config: $config->settings,
            spectatorSlots: $config->spectatorSlots,
        );

    }

    protected function bootCommands(): void
    {
        parent::bootCommands();

        $this->registerCommand(new RestartCommand);
        $this->registerCommand(new SetTeamCommand);
        $this->registerCommand(new SetTeamAllCommand);
        $this->registerCommand(new SwapTeamsCommand);
        $this->registerCommand(new ShuffleTeamsCommand);
        $this->registerCommand(new LockTeamsCommand);
    }

    protected function bootMap(): void
    {
        parent::bootMap();

        $gameLayer = $this->getMap()->getGameLayer();

        if ($gameLayer !== null) {
            PickupSpawner::spawn($this, $gameLayer, self::respawnTimes(), self::spawnDelays());
        }
    }

    protected function bootGameController(): void
    {
        $this->gameController = new GameController($this->tickHandler);
    }

    protected function createCharacterEntity(Vector2 $position): AbstractCharacterEntity
    {
        return new PvpCharacterEntity($this, $position);
    }

    /**
     * @return array<int, int>
     */
    private static function respawnTimes(): array
    {
        return [
            GameLayer::ENTITY_HEALTH_1       => 1750, // ~35s
            GameLayer::ENTITY_ARMOR_1        => 1250, // ~25s
            GameLayer::ENTITY_WEAPON_SHOTGUN => 250,  // ~5s
            GameLayer::ENTITY_WEAPON_GRENADE => 250,
            GameLayer::ENTITY_WEAPON_RIFLE   => 250,
            GameLayer::ENTITY_POWERUP_NINJA  => 3000, // ~60s
        ];
    }

    /**
     * @return array<int, int>
     */
    private static function spawnDelays(): array
    {
        return [
            GameLayer::ENTITY_POWERUP_NINJA => 4500, // 90s — matches Teeworlds 0.6 datafile default
        ];
    }
}
