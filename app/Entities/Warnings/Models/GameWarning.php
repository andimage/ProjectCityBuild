<?php

namespace App\Entities\Warnings\Models;

use App\Model;

/**
 * @deprecated
 */
final class GameWarning extends Model
{
    protected $table = 'game_network_warnings';

    protected $primaryKey = 'game_warning_id';

    protected $fillable = [
        'server_id',
        'warned_player_id',
        'warned_player_type',
        'staff_player_id',
        'staff_player_type',
        'reason',
        'weight',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function warnedPlayer()
    {
        return $this->morphTo(null, 'warned_player_type', 'warned_player_type');
    }

    public function staffPlayer()
    {
        return $this->morphTo(null, 'staff_player_type', 'staff_player_id');
    }
}
