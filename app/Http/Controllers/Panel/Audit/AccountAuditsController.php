<?php

namespace App\Http\Controllers\Panel\Audit;

use App\Entities\Accounts\Models\Account;
use App\Http\WebController;

class AccountAuditsController extends WebController
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Account $account)
    {
        $ledgers = $account->authoredLedgerEntries;

        return view('admin.auditing.user-index')->with(compact('account', 'ledgers'));
    }
}
