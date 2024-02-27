<?php

use App\modules\Korzilla\RKeeper\Actions\RKeeperGetListRestaurant;
use App\modules\Korzilla\RKeeper\Actions\RKeeperGetMenuAction;
use App\modules\Korzilla\RKeeper\Actions\RKeeperGetTokenSubAction;
use App\modules\Korzilla\RKeeper\Data\Repositories\RKeeperRepository;
use App\modules\Korzilla\RKeeper\Tasks\RKeeperGetTokenTask;
use App\modules\Korzilla\RKeeper\Tasks\RKeeperSendRequestTask;
use App\modules\Korzilla\RKeeper\Values\Inputs\RKeeperAuthInput;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
require_once $ROOTDIR . "/autoload.php";
global $db, $pathInc, $pathInc2, $catalogue, $isObjDB, $isObjDB2, $current_catalogue, $nc_core, $field_connect, $setting, $currencyArray;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);


$catalogueId = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']))['Catalogue_ID'];

GetSuggestionsAdress
$authInput = new RKeeperAuthInput(
    $catalogueId,
    "3c1819c4-417a-4275-bff0-bc5c52efa1f9",
    "ec3986c9-fb01-4686-a590-71091d4c41f5"
);


$rkRepository = new RKeeperRepository($db);


// $getMenuAc = new RKeeperGetMenuAction(
//     new RKeeperGetTokenSubAction($rkRepository,new RKeeperSendRequestTask()),
//     new RKeeperSendRequestTask()
// );

// var_dump($getMenuAc ->run($authInput));


// $getRestaurans = new RKeeperGetListRestaurant(
//     new RKeeperGetTokenSubAction($rkRepository,new RKeeperSendRequestTask()),
//     new RKeeperSendRequestTask()
// );

// var_dump($getRestaurans ->run($authInput));
