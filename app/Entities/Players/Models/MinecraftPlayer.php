<?php

namespace App\Entities\Players\Models;

use App\Entities\Accounts\Models\Account;
use App\Entities\Bans\BannableModelInterface;
use App\Entities\Bans\Models\GameBan;
use App\Library\Auditing\Contracts\Recordable;
use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class MinecraftPlayer extends Model implements BannableModelInterface, Recordable
{
    use HasFactory;
    use \App\Library\Auditing\Recordable;

    protected $table = 'players_minecraft';

    protected $primaryKey = 'player_minecraft_id';

    protected $fillable = [
        'uuid',
        'account_id',
        'last_synced_at',
    ];

    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'last_synced_at',
    ];

    public function getBanIdentifier(): string
    {
        return $this->uuid;
    }

    public function getBanReadableName(): ?string
    {
        $aliases = $this->aliases;
        if ($aliases->count() == 0) {
            return null;
        }

        return $this->aliases->last()->alias;
    }

    public function getDashedUuidAttribute()
    {
        $uuid = $this->uuid;

        return substr($uuid, 0, 8).'-'.substr($uuid, 8, 4).'-'.substr($uuid, 12, 4).'-'.substr($uuid, 16, 4).'-'.substr($uuid, 20);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function aliases(): HasMany
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

    /**
     * Update the last seen at time of the player to now.
     */
    public function touchLastSyncedAt(): bool
    {
        $this->last_synced_at = $this->freshTimestamp();

        return $this->save();
    }

    public function getPanelShowUrl(): string
    {
        return route('front.panel.minecraft-players.show', $this);
    }

    public function getHumanRecordName(): string
    {
        return "MC Player " . $this->uuid;
    }
}
