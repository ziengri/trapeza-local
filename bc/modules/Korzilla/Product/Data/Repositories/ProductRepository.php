<?php

namespace App\modules\Korzilla\Product\Data\Repositories;

use App\modules\Korzilla\Product\Models\ProductModel;
use App\modules\Ship\Parent\Repositories\Repository;

class ProductRepository extends Repository
{

    protected function getTableName(){
        return "Message2001";

    }

    protected function getIdName(){
        return "Message_ID";
    }

    protected function getModelClassName(){
        return ProductModel::class;
    }
    
}
