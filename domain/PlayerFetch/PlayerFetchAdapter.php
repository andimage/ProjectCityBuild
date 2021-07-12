<?php

namespace Domain\PlayerFetch;

use App\Library\Mojang\Models\MojangPlayer;

interface PlayerFetchAdapter
{
    /**
     * @param array $aliases
     * @param int|null $timestamp
     * @return MojangPlayer[]
     */
    public function fetch(array $aliases, ?int $timestamp): array;
}
