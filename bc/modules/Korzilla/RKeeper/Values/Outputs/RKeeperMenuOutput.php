<?php

namespace App\modules\Korzilla\RKeeper\Values\Outputs;

use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperCategoryDTO;
use App\modules\Korzilla\RKeeper\Values\DTO\RKeeperProductDTO;

class RKeeperMenuOutput
{
    /** @var string */
    public $name = null;

    /** @var RKeeperCategoryDTO[] */
    public $categories = null;

    /** @var RKeeperProductDTO[] */
    public $products = null;

    /** @var bool|null */
    public $haveChanges = null;
}
