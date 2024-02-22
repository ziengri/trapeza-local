<?php
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
GLOBAL $db, $pathInc, $pathInc2, $catalogue, $current_catalogue, $nc_core, $field_connect, $setting;

// получить ID сайта и параметры
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}
/*
Array
(
    [order_id] => 380414
    [order_state] => COMPLETED
    [reference] => 54
    [id] => 289526
    [date] => 2018.07.18 19:45:36
    [type] => PURCHASE
    [state] => APPROVED
    [reason_code] => 1
    [message] => Successful financial transaction
    [name] => UNKNOWN NAME
    [pan] => 480938******7444
    [email] => vsegta@mail.ru
    [amount] => 1000
    [fee] => 0
    [currency] => 643
    [approval_code] => 599348
    [signature] => Yzc1MzA5NzUwZWM3YTFkOTJmOWRiMTM1MzJjZTEzMGM=
)

*/

error_reporting(0);
header('Content-type: text/plain');

$xml = file_get_contents('php://input');
if (!$xml)
	die('Ошибка 1');

$xml = simplexml_load_string($xml);
if (!$xml)
	die('Ошибка 2');

$response = json_decode(json_encode($xml), true);
if (!$response || count($response) == 0)
	die('Ошибка 3');

if ($response['order_state']=='COMPLETED' && $response['reference']>0) {
	orderWasPayd($response['reference'],'Best2Pay');
}
//file_put_contents($ROOTDIR."/b2p.log",print_r($response,1));

die('ok');