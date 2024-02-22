<?
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
global $db, $pathInc, $pathInc2, $catalogue, $isObjDB, $isObjDB2, $current_catalogue, $nc_core, $field_connect, $setting, $currencyArray;


$subManager = new SubdivisionManager($nc_core);


$item2 = new SubdivisionDTO();
$item2->id = 7088;
$item2->name = "test7-1";
$item2->parentId = 1088;

$item3 = new SubdivisionDTO();
$item3->id = 7089;
$item3->name = "test7-2";
$item3->parentId = 1088;

$item = new SubdivisionDTO();
$item->id = 7088;
$item->name = "test7";
$item->parentId = 0;
$item->children = [$item2,$item3];


$subManager->run([$item]);

var_dump($subManager->getNewSubdivision());



// class NewSubdivisionDTO
// {
//     /**
//      *  @var int*/
//     public $subdivisionId;

//     /**
//      *  @var int*/
//     public $subClassId;

//     /** @var string*/
//     public $hiddenUrl;

// }

// class ExistingSubdivisionDTO
// {
//     /**
//      *  @var int*/
//     public $subdivisionId;

//     /**
//      *  @var int*/
//     public $parentSubId;

//     /** @var string*/
//     public $hiddenUrl;

//     /** @var int*/
//     public $checked;

//     /** @var bool*/
//     public $is_updating = false;

// }