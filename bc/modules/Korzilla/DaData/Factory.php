<?php

namespace App\modules\Korzilla\DaData;

use App\modules\Korzilla\DaData\OrganizationByINN;
use App\modules\Korzilla\DaData\Okved;

class Factory
{
    public static function create(string $type, $token)
    {
        if (!$token) throw new \Exception("Invalid token", 500);
        switch ($type) {
            case 'OrganizationByINN':
                return new OrganizationByINN($token);
                break;
            case 'Okved':
                return new Okved($token);
                break;
            default:
                throw new \Exception("Not found class", 500);
                break;
        }
        
    }
}
