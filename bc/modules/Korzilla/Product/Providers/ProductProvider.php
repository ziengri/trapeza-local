<?php

namespace App\modules\Korzilla\Product\Providers;

use App\modules\Korzilla\Product\Actions\ProductUploadAction;
use App\modules\Korzilla\Product\Data\Repositories\ProductRepository;
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
     * @param ProductSetInput $input -- массив разделов которые будут выгружаться
     * @param string|null $import_source -- ID раздела куда будут выгружаться разделы
     * @return ProductSetOutput[]
     */
    public function upload(ProductSetInput $input, string $import_source = null)
    {   
        //*Репозитории
        $productRepository = new ProductRepository($this->nc_core->db);
        
        $productUploaderAction = new ProductUploadAction($this->nc_core,$productRepository);
        return $productUploaderAction->run($input,$import_source);
    }

}
