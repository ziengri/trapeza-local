<?php
// $dir = str_replace('Korzilla/Excel/Export', '', __DIR__);
// require_once $dir . '/default/include_console/include_console.php';

use App\modules\Korzilla\MySklad\Auth;
use App\modules\Korzilla\MySklad\Products;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";

Auth::getToken('admin@ilsur1', '99c3440435');

$products = new Products();
$products->getProductList(Auth::$token, ['expand' => 'images', 'limit' => 100]);