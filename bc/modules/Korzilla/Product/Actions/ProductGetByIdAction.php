<?php

namespace App\modules\Korzilla\Product\Actions;

use App\modules\Korzilla\Product\Data\Repositories\ProductRepository;
use App\modules\Korzilla\Product\Models\ProductModel;


class ProductGetByIdAction
{



    /** @var ProductRepository*/
    private $productRepository;


    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    /**
     * Undocumented function
     *
     * @param integer $id
     * @param integer $catalogueId
     * @return ProductModel|null
     */
    public function run(int $id, int $catalogueId)
    {
        $queryWhere = [];

        $queryWhere[] = ['Catalogue_ID', $catalogueId, '='];
        $queryWhere[] = ['Message_ID', $id, '='];


        /** @var ProductModel $productModel*/
        return $this->productRepository->getRow($queryWhere);


    }

}
