<?php

namespace App;

use TeeFrame\Core\TickHandler;
use TeeFrame\Game\AbstractWorld;
use TeeFrame\Game\Commands\ChangeGameModeCommand;
use TeeFrame\Game\Commands\ChangeMapCommand;
use TeeFrame\Game\Commands\ReloadCommand;
use TeeFrame\Game\Entities\Character\AbstractCharacterEntity;
use TeeFrame\Game\Entities\Character\PvpCharacterEntity;
use TeeFrame\Game\Tees\AbstractTee;
use TeeFrame\Game\World\PickupSpawner;
use TeeFrame\Game\World\Vector2;
use TeeFrame\Map\Map;
use TeeFrame\Map\MapLayers\GameLayer;
use TeeFrame\Server\AbstractServerInstance;

class World extends AbstractWorld
{
    protected GameController $dmController;

    public function __construct(
        public string $identifier,
        protected TickHandler $tickHandler,
        protected AbstractServerInstance $server,
        int $slotsLimit,
    ) {
        parent::__construct($identifier, $tickHandler, new Map('maps/dm1.map'), $server, $slotsLimit);

        foreach (['dm1', 'ctf1'] as $mapName) {
            $command = new ChangeMapCommand($mapName);
            $this->registerCommand($command);
            $this->getVoteController()->addVoteOption($command->getDescription(), $command->getCommand());
        }

        foreach (['dm', 'tdm', 'ctf'] as $gameMode) {
            $command = new ChangeGameModeCommand($gameMode, mapName: $gameMode === 'ctf' ? 'ctf1' : null);
            $this->registerCommand($command);
            $this->getVoteController()->addVoteOption($command->getDescription(), $command->getCommand());
        }

        $command = new ReloadCommand;
        $this->registerCommand($command);
        $this->getVoteController()->addVoteOption($command->getDescription(), $command->getCommand());
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

    protected function getNewCharacterEntity(Vector2 $position): AbstractCharacterEntity
    {
        return new PvpCharacterEntity($this, $position);
    }

    public function getMotd(AbstractTee $requestingTee): string
    {
        return 'Welcome to TeeFrame '.$this->getGameController()->getGameType().'!';
    }

    /**
     * @return array<int, int>
     */
    private static function respawnTimes(): array
    {
        return [
            GameLayer::ENTITY_HEALTH_1      => 1750, // ~35s
            GameLayer::ENTITY_ARMOR_1       => 1250, // ~25s
            GameLayer::ENTITY_WEAPON_SHOTGUN => 250,  // ~5s
            GameLayer::ENTITY_WEAPON_GRENADE => 250,
            GameLayer::ENTITY_WEAPON_RIFLE  => 250,
            GameLayer::ENTITY_POWERUP_NINJA => 3000, // ~60s
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
