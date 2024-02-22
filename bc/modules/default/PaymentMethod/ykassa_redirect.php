<?php
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
require_once $ROOTDIR.'/bc/modules/default/PaymentMethod/class_payment_method.php';

$PaymentMethod = new PaymentMethod();


GLOBAL $db, $catalogue, $current_catalogue, $nc_core, $setting;

if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

$message = securityForm($_GET['message']);
$params = ['yaShopId' => $setting['yaShopId'], 'yaScid' => $setting['yaScid']];

$PaymentMethod->stastusOplatyYkassa($message, $params);

header('Location: /');
