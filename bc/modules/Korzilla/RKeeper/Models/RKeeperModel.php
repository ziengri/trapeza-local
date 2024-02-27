<?php

namespace App\modules\Korzilla\RKeeper\Models;
use App\modules\Ship\Parent\Models\Model;

class RKeeperModel extends Model
{
    /** @var integer */
    public $id = null;

    /** @var string */
    public $token;

    /** @var integer */
    public $expires_at;

    /** @var integer */
    public $Catalogue_ID;

    
}
