<?php

namespace App\modules\Korzilla\Product\Providers;

use App\modules\Korzilla\Product\Actions\ProductGetByIdAction;
use App\modules\Korzilla\Product\Actions\ProductUploadAction;
use App\modules\Korzilla\Product\Data\Repositories\ProductRepository;
use App\modules\Korzilla\Product\Models\ProductModel;
use App\modules\Korzilla\Product\Values\Inputs\ProductSetInput;
use App\modules\Korzilla\Product\Values\Outputs\ProductSetOutput;
use App\modules\Ship\Parent\Providers\Provider;

class ProductProvider extends Provider
{    
    
    private $setting;
    private $nc_core;

    public function __construct(\nc_Core $nc_core, array $setting){
        $this->setting = $setting;
        $this->nc_core = $nc_core;
    }

    /**
     * Undocumented function
     *
     * @param ProductSetInput[] $input -- массив продуктов которые будут выгружаться
     * @param string|null $import_source
     * @return ProductSetOutput[]
     */
    public function upload(array &$input, string $import_source = null)
    {   
        //*Репозитории
        $productRepository = new ProductRepository($this->nc_core->db);
        
        $productUploaderAction = new ProductUploadAction($this->nc_core,$productRepository);
        return $productUploaderAction->run($input,$import_source);
    }


    /**
     * Undocumented function
     *
     * @param integer $id
     * @param integer $catalogueId
     * @return ProductModel|null
     */
    public function getById(int $id, int $catalogueId)
    {   
        //*Репозитории
        $productRepository = new ProductRepository($this->nc_core->db);
        
        $productGetByIdAction = new ProductGetByIdAction($productRepository);
        return $productGetByIdAction->run($id,$catalogueId);
    }

}
