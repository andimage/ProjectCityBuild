<?php

namespace App\Library\Auditing;

use Altek\Accountant\Models\Ledger;

/**
 * Trait FilterableAccountable
 * Apply to classes which are accountable to allow filtering.
 */
trait FilterableAccountable
{
    public function authoredLedgerEntries()
    {
        return $this->morphMany(Ledger::class, 'accountable');
    }
}