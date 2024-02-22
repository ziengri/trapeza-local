<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

use App\modules\Korzilla\Cart\Model\Order;
use Custom\Cart\Model\CustomOrder;

Authorize();

global $nc_core, $current_catalogue, $current_user, $catalogue;


if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

if (class_exists('Custom\Cart\Model\CustomOrder')) {
    $Order = new CustomOrder($_POST['type_order']);
} else {
    $Order = new Order($_POST['type_order']);
}

switch ($_POST['action']) {
    case 'add':
        $itemObject = \Class2001::getItemById($_POST['id']);
        $Order->addItemOrder($itemObject, $_POST);
        $Order->setTotalSum();
        $Order->setOrderInSession();
        $result = $Order->getResult();
        echo json_encode(array_merge($result, ['html' => $Order->getFormBuyOneClick($result['order'])]));
        break;
    case 'update_count':
        $itemObject = \Class2001::getItemById($_POST['id']);
        $Order->recalculationCount($itemObject, $_POST);
        $Order->setTotalSum();
        $Order->setOrderInSession();
        echo json_encode($Order->getResult());
        break;
    case 'delete':
        break;
    case "delivery":
        $Order->setDelivery($_POST);
        $Order->setTotalSum();
        $Order->setOrderInSession();
        echo json_encode($Order->getResult());
        break;
}


 
