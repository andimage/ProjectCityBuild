<?php

namespace App\Http\Actions\GameBans;

use App\Entities\Bans\Models\GameBan;
use App\Entities\GamePlayerType;
use App\Entities\ServerKeys\Models\ServerKey;
use App\Services\PlayerBans\Exceptions\UnauthorisedKeyActionException;
use App\Services\PlayerBans\Exceptions\UserAlreadyBannedException;
use App\Http\Actions\Players\GetOrCreatePlayer;

final class CreatePlayerBan
{
    private $getOrCreatePlayer;
    private $getPlayerBans;

    public function __construct(GetOrCreatePlayer $getOrCreatePlayer, GetPlayerBans $getPlayerBans)
    {
        $this->getOrCreatePlayer = $getOrCreatePlayer;
        $this->getPlayerBans = $getPlayerBans;
    }

    public function execute(
        ServerKey $serverKey,
        int $serverId,
        string $bannedPlayerId,
        GamePlayerType $bannedPlayerType,
        string $bannedAliasAtTime,
        string $staffPlayerId,
        GamePlayerType $staffPlayerType,
        ?string $banReason,
        ?int $banExpiresAt = null,
        bool $isGlobalBan = false
    ) : GameBan {

        if ($isGlobalBan && !$serverKey->can_global_ban) {
            throw new UnauthorisedKeyActionException('limited_key', 'This server key does not have permission to create global bans');
        }
 
        $bannedPlayer = $this->getOrCreatePlayer->execute(
            $bannedPlayerType, 
            $bannedPlayerId
        );

        $staffPlayer  = $this->getOrCreatePlayer->execute(
            $staffPlayerType, 
            $staffPlayerId
        );

        $activeBan = $this->getPlayerBans->execute(
            true, 
            $bannedPlayer->getKey(), 
            $bannedPlayerType
        );
 
         if ($activeBan !== null) {
             throw new UserAlreadyBannedException('player_already_banned', 'Player is already banned');
         }

         return GameBan::create([
            'server_id'             => $serverId,
            'banned_player_id'      => $bannedPlayer->getKey(),
            'banned_player_type'    => $bannedPlayerType->valueOf(),
            'banned_alias_at_time'  => $bannedAliasAtTime,
            'staff_player_id'       => $staffPlayer->getKey(),
            'staff_player_type'     => $staffPlayerType->valueOf(),
            'reason'                => $banReason,
            'is_active'             => true,
            'is_global_ban'         => $isGlobalBan,
            'expires_at'            => $banExpiresAt ? Carbon::createFromTimestamp($banExpiresAt) : null,
        ]);
    }
}