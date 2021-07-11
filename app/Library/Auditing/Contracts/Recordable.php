<?php

namespace App\Library\Auditing\Contracts;

interface Recordable extends \Altek\Accountant\Contracts\Recordable
{
    /**
     * Get the URL to show the user a record.
     */
    public function getPanelShowUrl(): string;


    /**
     * Get a human readable version of this model for ledgers
     */
    public function getHumanRecordName(): string;

    /**
     * Get the URL of the audit list for this model
     */
    public function getAuditListUrl(): string;
}
