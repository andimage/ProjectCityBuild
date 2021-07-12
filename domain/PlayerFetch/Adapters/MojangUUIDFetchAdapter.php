<?php

namespace Domain\PlayerFetch\Adapters;

use App\Library\Mojang\Api\MojangPlayerApi;
use Domain\PlayerFetch\PlayerFetchAdapter;

final class MojangUUIDFetchAdapter implements PlayerFetchAdapter
{
    private MojangPlayerApi $mojangPlayerApi;

    public function __construct(MojangPlayerApi $mojangPlayerApi)
    {
        $this->mojangPlayerApi = $mojangPlayerApi;
    }

    public function fetch(array $aliases, ?int $timestamp): array
    {
        // Split names into chunks because the Mojang API won't allow more
        // than 10 names per batch
        $names = collect($aliases)->chunk(10);

        $players = [];
        foreach ($names as $nameChunk) {
            $response = $this->mojangPlayerApi->getUuidBatchOf($nameChunk->toArray());
            $response = array_map(function (MojangPlayer $player) {
                $uuid = $player->getUuid();

                return str_replace('-', '', $uuid);
            }, $response);

            $players = array_merge($players, $response);
        }

        return $players;
    }
}
