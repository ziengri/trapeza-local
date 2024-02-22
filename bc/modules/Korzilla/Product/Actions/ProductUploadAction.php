<?php

namespace App\modules\Korzilla\Product\Actions;

use App\modules\Korzilla\Product\Data\Repositories\ProductRepository;
use App\modules\Korzilla\Product\Models\ProductModel;
use App\modules\Korzilla\Product\Values\Inputs\ProductSetInput;
use App\modules\Korzilla\Product\Values\Outputs\ProductSetOutput;
use App\modules\Ship\Helpers\FieldNotSet;

class ProductUploadAction
{

    /** @var \nc_Db*/
    private $db;


    /** @var \nc_Core*/
    private $core;

    /** @var ProductRepository*/
    private $productRepository;


    /** @var ProductSetOutput[]*/
    private $productSetOutput = [];


    public function __construct(
        \nc_Core $core,
        ProductRepository $productRepository
    ) {
        $this->db = $core->db;
        $this->productRepository = $productRepository;
    }

    /**
     * Undocumented function
     *
     * @param ProductSetInput $input
     * @return ProductSetOutput[]
     */
    public function run(ProductSetInput $input, string $import_source): array
    {
        $timeExport = time();

        $exsistingProduct = $this->checkProductExist($input, $import_source);

        //Если не существует товара в БД
        if ($exsistingProduct === false) {
            $this->create($input, $timeExport, $import_source);
        } else {
            $this->update($input, $exsistingProduct, $timeExport, $import_source);
        }

        return $this->productSetOutput;


    }

    /**
     * @param ProductSetInput $productInput
     * @param integer $timeExport
     * @param string $import_source
     * @return void
     */
    private function create(ProductSetInput $productInput, int $timeExport, string $import_source)
    {   
        $newProduct = new ProductModel();

        foreach ($productInput as $key => $value) {
            if($value == FieldNotSet::CONSTANT){
                continue;
            }
            $newProduct->$key = $value;
        }
        
        $newProduct->Catalogue_ID = $productInput->Catalogue_ID;
        $newProduct->Sub_Class_ID = $productInput->Sub_Class_ID;
        $newProduct->Subdivision_ID = $productInput->Subdivision_ID;
        $newProduct->Keyword =  encodestring(trim($productInput->name)." ".trim(($productInput->art2 ? $productInput->art2 : $productInput->art)),1);
        $newProduct->name = $productInput->name;
        $newProduct->import_source = $import_source;
        $newProduct->timestamp_export = $timeExport;

        if(!$this->productRepository->save($newProduct)){
            throw new \Exception($this->db->last_query);
            //! Переписать на логирование и вывод ошибки
        }

        $this->productSetOutput[$newProduct->Message_ID] = ProductSetOutput::fromModel($newProduct);
    }

    private function update(ProductSetInput $productInput, ProductModel $exsistingProduct, int $timeExport, string $import_source)
    {   

        foreach ($productInput as $key => $value) {
            if($value == FieldNotSet::CONSTANT){
                continue;
            }
            $exsistingProduct->$key = $value;
        }

        $exsistingProduct->Catalogue_ID = $productInput ->Catalogue_ID;
        $exsistingProduct->Keyword =  encodestring(trim($productInput->name)." ".trim(($productInput->art2 ? $productInput->art2 : $productInput->art)),1);
        $exsistingProduct->name = $productInput->name;
        $exsistingProduct->import_source = $import_source;
        $exsistingProduct->timestamp_export = $timeExport;

        if(!$this->productRepository->save($exsistingProduct)){
            throw new \Exception($this->db->last_query);
            //! Переписать на логирование и вывод ошибки
        }

        $this->productSetOutput[$exsistingProduct->Message_ID] = ProductSetOutput::fromModel($exsistingProduct);
    }


    /**
     * Undocumented function
     *
     * @param ProductSetInput $productInput
     * @param [type] $import_source
     * @return ProductModel|bool
     */
    private function checkProductExist(ProductSetInput $productInput, $import_source)
    {

        $queryWhere = [];

        $queryWhere[] = ['Catalogue_ID', $productInput ->Catalogue_ID, '='];
        $queryWhere[] = ['Keyword', encodestring(trim($productInput->name)." ".trim(($productInput->art2 ? $productInput->art2 : $productInput->art)),1)   , '='];


        /** @var ProductModel $productModel*/
        $productModel = $this->productRepository->getRow($queryWhere);

        if (!$productModel) {
            return false;
        }

        if ($import_source != $productModel->import_source) {
            return false;
        }

        return $productModel;
    }
}
