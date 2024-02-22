<?php

namespace App\modules\Korzilla\SMSCenter;

use App\modules\Korzilla\SMSCenter\SMSC\Controller as SMSC;
use App\modules\Korzilla\SMSCenter\SMS\Controller as SMS;
use App\modules\Korzilla\SMSCenter\ISMSCenter;

class SMSCenterFactory
{
    public static function SMSC(string $login, string $password): ISMSCenter
    {
        return new SMSC($login, $password);
    }

    public static function SMS(string $apiKey): ISMSCenter
    {
        return new SMS($apiKey);
    }
}
