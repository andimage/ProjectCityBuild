<?php

namespace App\Library\Auditing;

use Altek\Accountant\Contracts\Cipher;
use Altek\Accountant\Exceptions\DecipherException;

class FullRedact implements Cipher
{
    // This special token is unlikely to appear normally, use it to signal
    // a field has been totally redacted, and this can then be checked for the UI
    const REDACTION_TOKEN = '!!PCB-REDACTED!!';

    public static function isOneWay(): bool
    {
        return true;
    }

    public static function cipher($value): string
    {
        return FullRedact::REDACTION_TOKEN;
    }

    public static function decipher($value)
    {
        throw new DecipherException('Value deciphering is not supported by this implementation', $value);
    }
}
