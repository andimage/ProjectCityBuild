<?php

namespace Domain\PlayerFetch;

use App\Entities\GameType;
use App\Library\Mojang\Api\MojangPlayerApi;
use Domain\PlayerFetch\Adapters\MojangUUIDFetchAdapter;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;

final class PlayerFetchService
{
    private PlayerFetchRepository $playerRepository;

    public function __construct(PlayerFetchRepository $playerRepository)
    {
        $this->playerRepository = $playerRepository;
    }

    public function fetchSynchronously(GameType $gameType, array $aliases, ?int $timestamp = null)
    {
        $adapter = $this->getAdapter($gameType);
        $players = $adapter->fetch($aliases, $timestamp);

        foreach ($players as $player) {
            $this->playerRepository->createPlayerIfNotExist(
                $player->getAlias(),
                $player->getUuid()
            );
        }
    }

    private function getAdapter(GameType $gameType): PlayerFetchAdapter
    {
        switch ($gameType->valueOf()) {
            case GameType::Minecraft:
                return new MojangUUIDFetchAdapter(new MojangPlayerApi());
            default:
                throw new UnsupportedGameException($gameType->name()." is not supported");
        }
    }
}
