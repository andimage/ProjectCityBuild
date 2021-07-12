<?php

namespace Domain\PlayerFetch;

use App\Library\Mojang\Models\MojangPlayer;

interface PlayerFetchAdapter
{
    /**
     * @return MojangPlayer[]
     */
    public function fetch(array $aliases, ?int $timestamp): array;
}
