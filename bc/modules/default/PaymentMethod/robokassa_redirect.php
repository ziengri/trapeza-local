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

$message = securityForm($_REQUEST["InvId"]);
$out_summ = $_REQUEST["OutSum"];
$crc = $_REQUEST["SignatureValue"];
$params = ['out_summ' => $out_summ, 'crc' => $crc, 'rkassaLogin' => $setting['rkassaLogin'], 'rkassaPass1' => $setting['rkassaPass1'], 'rkassaPass2' => $setting['rkassaPass2']];


$logdata = array(
	"REQUEST"=>$_REQUEST,
	"POST"=>$_POST,
	"GET"=>$_POST,
	"params"=>$params
);


//file_put_contents($ROOTDIR.'/bc/modules/default/PaymentMethod/logRKassa/status.log', "\n---- ".date("d.m.Y H:i:s")." ----\n".print_r($logdata, 1), FILE_APPEND);


echo $PaymentMethod->stastusOplatyRobokassa($message, $params);


