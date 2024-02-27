<?php

namespace App\modules\Korzilla\RKeeper\Data\Repositories;

use App\modules\Korzilla\RKeeper\Models\RKeeperModel;
use App\modules\Ship\Parent\Repositories\Repository;

class RKeeperRepository extends Repository
{

    protected function getTableName(){
        return "RKeeper_Token";

    }

    protected function getIdName(){
        return "id";
    }

    protected function getModelClassName(){
        return RKeeperModel::class;
    }

}
