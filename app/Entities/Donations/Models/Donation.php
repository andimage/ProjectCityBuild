<?php

namespace App\Entities\Donations\Models;

use App\Entities\Accounts\Models\Account;
use App\Library\Auditing\Contracts\Recordable;
use App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class Donation extends Model implements Recordable
{
    use HasFactory;
    use \App\Library\Auditing\Recordable;

    /**
     * Amount that needs to be donated to be granted
     * lifetime perks.
     */
    const LIFETIME_REQUIRED_AMOUNT = 30;

    /**
     * Amount that needs to be donated to receive one month
     * worth of donator perks.
     */
    const ONE_MONTH_REQUIRED_AMOUNT = 3;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'donations';

    protected $primaryKey = 'donation_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'amount',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }

    public function perks(): HasMany
    {
        return $this->hasMany(DonationPerk::class, 'donation_id', 'donation_id');
    }

    public function getPanelShowUrl(): string
    {
        return route('front.panel.donations.show', $this);
    }

    public function getHumanRecordName(): string
    {
        return "Donation #" . $this->getKey();
    }
}
