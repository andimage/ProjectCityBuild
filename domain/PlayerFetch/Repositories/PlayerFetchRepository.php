<?php

namespace Domain\PlayerFetch;

use App\Entities\Players\Models\MinecraftPlayer;
use App\Entities\Players\Models\MinecraftPlayerAlias;
use DB;

final class PlayerFetchRepository
{
    public function createPlayerIfNotExist(string $alias, string $uuid)
    {
        if (MinecraftPlayer::where('uuid', $uuid)->first()) {
            return;
        }

        DB::beginTransaction();
        $player = MinecraftPlayer::create([
            'uuid' => $uuid,
            'account_id' => null,
            'last_synced_at' => now(),
        ]);
        MinecraftPlayerAlias::create([
            'player_minecraft_id' => $player->getKey(),
            'alias' => $alias,
            'registered_at' => now(),
        ]);
        DB::commit();
    }
}
