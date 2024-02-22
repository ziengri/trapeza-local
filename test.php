<?php

use App\modules\Korzilla\Product\Providers\ProductProvider;
use App\modules\Korzilla\Product\Values\Inputs\ProductSetInput;
use App\modules\Korzilla\Product\Values\Outputs\ProductSetOutput;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubClassRepository;
use App\modules\Korzilla\Subdivision\Providers\SubdivisionProvider;
use App\modules\Korzilla\Subdivision\Data\Repositories\SubdivisionRepository;
use App\modules\Korzilla\Subdivision\Tasks\SubdivisionGetAllTask;
use App\modules\Korzilla\Subdivision\Tasks\SubdivisionGetRootTask;
use App\modules\Korzilla\Subdivision\Values\DTO\SubdivisionDataDTO;
use App\modules\Korzilla\Subdivision\Values\Inputs\SubdivisionSetInput;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
require_once $ROOTDIR . "/autoload.php";
global $db, $pathInc, $pathInc2, $catalogue, $isObjDB, $isObjDB2, $current_catalogue, $nc_core, $field_connect, $setting, $currencyArray;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


$data = json_decode(file_get_contents("test.json"),1);

/** @var SubdivisionSetInput[] $categories */
$categories = [];


foreach ($data['result']['categories'] as $key => $category) {
    $dtoCategory = new SubdivisionSetInput();
    $dtoCategory->id = $category['id'];
    $dtoCategory->Subdivision_Name = $category['name'];
    $dtoCategory->parentId = $category['parentId'];
    // echo $dtoCategory->parentId . "<br>";
    $categories[]= $dtoCategory;
}

// exit();
$subdivisionProvider =  new SubdivisionProvider($nc_core,$setting);

//! ДОЛЖЕН ВОЗРАЩАТЬС SubdiviosonOutput а воозращет DataDTO ???ПОД ВОПРОСОМ
$subdivisionOutput = $subdivisionProvider->upload($categories);

var_dump($subdivisionOutput);
$catalogueId = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']))['Catalogue_ID'];


$productProvider =  new ProductProvider($nc_core,$setting);

/** @var ProductSetOutput[] $categories */
$productOutput;


foreach ($data['result']['products'] as $key => $product) {
    $dtoProduct = new ProductSetInput();
    $dtoProduct->code = $product['id'];
    $dtoProduct->Catalogue_ID = $catalogueId;
    $dtoProduct->Checked = (int)!$product['disabled'];
    $dtoProduct->name = $product['name'];
    $dtoProduct->price = (float)$product['price'];
    $dtoProduct->Created = date('Y-m-d H:i:s');

    
    //TODO Загрузка фотографий

    /**
     * @var SubdivisionDataDTO $curentSubdivision
     */
    $curentSubdivision =  $subdivisionOutput[$product['categoryId']];

    //! ID МОГУТ БЫТЬ ПЫСТЫМИ ИСПРАВИТЬ
    $dtoProduct->Subdivision_ID = $curentSubdivision->Subdivision_ID;
    $dtoProduct->Sub_Class_ID = $curentSubdivision->subclassId;

    var_dump($dtoProduct);

    $productOutput[] = $productProvider->upload($dtoProduct,"test");

}


var_dump($productOutput);


// $subdivisionProvider = new SubdivisionProvider($nc_core,$setting);

// var_dump($subdivisionProvider->getSubdivisionManagerUploader());



// $subdivisionRepository = new SubdivisionRepository($nc_core->db);
// $subClassRepository = new SubClassRepository($nc_core->db);


// // $getAllTask = new SubdivisionGetAllTask($subdivisionRepository);

// $getRootSubdivisionTask = new SubdivisionGetRootTask($subdivisionRepository,$subClassRepository);
// // var_dump($getAllTask->run("1077"));
// var_dump($getRootSubdivisionTask->run("1077"));