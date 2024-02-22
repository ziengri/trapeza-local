<?php

namespace App\modules\Korzilla\Permission;

class Controller
{
    public static function isDeveloper($userID)
    {
        if (!file_exists('/var/www/krza/data/modules/developers_id.php')) return false;
        require_once '/var/www/krza/data/modules/developers_id.php';
        return in_array($userID, DEVELOPERS_ID);
    }

    public static function isSeoDeveloper($userID)
    {
        if (!file_exists('/var/www/krza/data/modules/developers_id.php')) return false;
        require_once '/var/www/krza/data/modules/developers_id.php';
        return in_array($userID, SEO_DEVELOPERS_ID);
    }
}