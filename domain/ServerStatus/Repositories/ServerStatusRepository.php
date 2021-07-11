<?php

namespace Domain\ServerStatus\Repositories;

use App\Entities\Servers\Models\ServerStatus;
use Domain\ServerStatus\Entities\ServerQueryResult;

final class ServerStatusRepository
{
    public function store(int $serverId, ServerQueryResult $result, int $time): ServerStatus
    {
        return ServerStatus::create([
            'server_id' => $serverId,
            'is_online' => $result->isOnline,
            'num_of_players' => $result->numOfPlayers ?? 0,
            'num_of_slots' => $result->numOfSlots ?? 0,
        ]);
    }
}
