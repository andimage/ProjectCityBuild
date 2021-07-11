<?php

namespace App\Entities;

use App\Enum;

/**
 * List of games PCB supports.
 */
final class GameType extends Enum
{
    public const Minecraft = 1;
    public const Terraria = 2;
    public const Starbound = 3;

    public function name(): ?string
    {
        switch ($this->value) {
            case self::Minecraft:
                return 'minecraft';
            default:
                return null;
        }
    }
}
