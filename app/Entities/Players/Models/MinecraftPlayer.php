<?php

namespace App\Entities\Players\Models;

use App\Entities\Bans\Models\GameBan;
use App\Model;
use App\Entities\Bans\BannableModelInterface;
use App\Entities\Accounts\Models\Account;

final class MinecraftPlayer extends Model implements BannableModelInterface
{
    protected $table = 'players_minecraft';

    protected $primaryKey = 'player_minecraft_id';

    protected $fillable = [
        'uuid',
        'account_id',
        'playtime',
        'last_seen_at',
    ];

    protected $hidden = [

    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_seen_at',
    ];


    /**
     * {@inheritDoc}
     */
    public function getBanIdentifier(): string
    {
        return $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function getBanReadableName(): string
    {
        return $this->aliases->last()->alias;
    }


    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function aliases()
    {
        return $this->hasMany(MinecraftPlayerAlias::class, 'player_minecraft_id', 'player_minecraft_id');
    }

    public function gameBans()
    {
        return $this->morphMany(GameBan::class, 'banned_player');
    }

    public function isBanned()
    {
        return $this->gameBans()->where('is_active', true)->count() > 0;
    }
}