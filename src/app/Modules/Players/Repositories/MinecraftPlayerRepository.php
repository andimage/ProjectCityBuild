<?php
namespace App\Modules\Players\Repositories;

use App\Modules\Players\Models\MinecraftPlayer;
use App\Shared\Repository;
use Carbon\Carbon;

class MinecraftPlayerRepository extends Repository {

    protected $model = MinecraftPlayer::class;

    /**
     * Creates a new MinecraftPlayer
     *
     * @param int $userId
     * @return GameUser
     */
    public function store(
        string $uuid,
        Carbon $lastSeenAt = null,
        ?int $accountId = null,
        int $playTime = 0
    ) : MinecraftPlayer {

        return $this->getModel()->create([
            'uuid'          => $uuid,
            'account_id'    => $accountId,
            'playtime'      => $playTime,
            'last_seen_at'  => $lastSeenAt ?: Carbon::now(),
        ]);
    }

    public function getByUuid(string $uuid) : ?MinecraftPlayer {
        return $this->getModel()
            ->where('uuid', $uuid)
            ->first();
    }

}