<?php

namespace Domain\ServerStatus;

use App\Entities\GameType;
use Domain\ServerStatus\Adapters\MinecraftQueryAdapter;
use Domain\ServerStatus\Exceptions\UnsupportedGameException;

final class ServerQueryAdapterFactory implements ServerQueryAdapterFactoryContract
{
    public function make(GameType $gameType): ServerQueryAdapter
    {
        switch ($gameType->valueOf()) {
            case GameType::Minecraft:
                return new MinecraftQueryAdapter();
            default:
                throw new UnsupportedGameException($gameType->name().' cannot be queried');
        }
    }
}
