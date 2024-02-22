<?php


namespace App\modules\Korzilla\Subdivision\Data\Repositories;

use App\modules\Korzilla\Subdivision\Models\SubdivisionModel;
use App\modules\Ship\Parent\Repositories\Repository;

class SubdivisionRepository extends Repository{


    protected function getTableName(){
        return "Subdivision";

    }

    protected function getIdName(){
        return "Subdivision_ID";
    }

    protected function getModelClassName(){
        return SubdivisionModel::class;
    }

}
