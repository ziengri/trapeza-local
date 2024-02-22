<?php

namespace App\modules\Korzilla\Subdivision\Data\Repositories;

use App\modules\Korzilla\Subdivision\Models\SubClassModel;
use App\modules\Ship\Parent\Repositories\Repository;

class SubClassRepository extends Repository
{

    protected function getTableName(){
        return "Sub_Class";

    }

    protected function getIdName(){
        return "Sub_Class_ID";
    }

    protected function getModelClassName(){
        return SubClassModel::class;
    }

}
