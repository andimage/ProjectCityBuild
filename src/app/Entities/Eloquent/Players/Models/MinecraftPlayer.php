<?php

namespace App\Entities\Eloquent\Players\Models;

use App\Model;
use App\Entities\Eloquent\Bans\BannableModelInterface;
use App\Entities\Eloquent\Accounts\Models\Account;

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
        return $this->aliases->first()->alias;
    }

    
    public function account()
    {
        return $this->hasOne(Account::class, 'account_id', 'account_id');
    }

    public function aliases()
    {
        return $this->hasMany(MinecraftPlayerAlias::class, 'player_minecraft_id', 'player_minecraft_id');
    }
}