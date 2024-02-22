<?php

namespace App\modules\Korzilla\SMSCenter;

interface ISMSCenter
{
    public function push(string $phone, string $message): array;
    public function call(string $phones): array;
}
