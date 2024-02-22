<?php 
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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


if ($_GET['zakaz']>0) {
	$ordArray = get_order($_GET['zakaz']);
	if ($ordArray['Message_ID']) {
		$orderid = $ordArray['Message_ID'];
		$order_total = $ordArray['totalSum'];
		$billing_email = $ordArray['email'];
	}
}

if (!$orderid && !$_REQUEST["id"]) die();

$sector = $setting['best2pay-sector'];
$password = $setting['best2pay-pass'];

$twostepsmode = 1;
$currency = '643';
$callback_url = "http://".$_SERVER['HTTP_HOST']."/bc/modules/bitcat/b2p.php";
$best2pay_url = "https://pay.best2pay.net";
$best2pay_operation = "Purchase";

if ($setting['best2pay-test']) $best2pay_url = "https://test.best2pay.net";

if ($twostepsmode == "1") {
	$best2pay_operation = "Authorize";
}

$signature  = base64_encode(md5($sector.intval($order_total * 100).$currency.$password));

/* ################## регистрация оплаты, получение ссылки на страницу оплаты ################## */

if (!isset($_REQUEST["id"]) && !isset($_REQUEST["operation"]) && !isset($_REQUEST["reference"])) {
	
	$context1 = array(
				'sector' => $sector,
				'reference' => $orderid,
				'amount' => intval($order_total * 100),
				'description' => "Заказ № {$orderid}",
				'email' => $billing_email,
				'currency' => $currency,
				'mode' => 1,
				'url' => $callback_url,
				'signature' => $signature
			);   


	$b2p_order_id = file_get_contents1($best2pay_url.'/webapi/Register',"",$context1);
	if (intval($b2p_order_id) == 0) die();
	$signature = base64_encode(md5($sector.$b2p_order_id.$password));

	// изменение статуса на "принят"
	//$order->update_status('on-hold');

	header("Location:{$best2pay_url}/webapi/{$best2pay_operation}?sector={$sector}&id={$b2p_order_id}&signature={$signature}");

}


/* ################## проверка заказа от best2pay, редирект  ################## */

if ($_REQUEST["id"] && $_REQUEST["operation"] && $_REQUEST["reference"]) {

		// check payment status
		$b2p_order_id = intval($_REQUEST["id"]);
		if (!$b2p_order_id)	return false;

		$b2p_operation_id = intval($_REQUEST["operation"]);
		if (!$b2p_operation_id) {
			$order_id = intval($_REQUEST["reference"]);
			$order = get_order($order_id);
			if ($order) {
				echo $response->message;
			}
			header('Location: http://'.$_SERVER[HTTP_HOST].'/bc/modules/bitcat/b2p.php?zakaz='.$order_id);
			exit();
		}

		// check payment operation state
		$signature = base64_encode(md5($sector.$b2p_order_id.$b2p_operation_id.$password));

		$best2pay_url = "https://pay.best2pay.net";
		if ($setting['best2pay-test']) $best2pay_url = "https://test.best2pay.net";

		$context  = array(
					'sector' => $sector,
					'id' => $b2p_order_id,
					'operation' => $b2p_operation_id,
					'signature' => $signature
				);

		$repeat = 3;

		while ($repeat) {

			$repeat--;

			// pause because of possible background processing in the Best2Pay
			sleep(2);

			$xml = file_get_contents1($best2pay_url.'/webapi/Operation', "", $context);

			if (!$xml)
				break;
			$xml = simplexml_load_string($xml);
			if (!$xml)
				break;
			$response = json_decode(json_encode($xml));
			if (!$response)
				break;

			if (!orderAsPayed($response)) continue;

			echo "Заказ не оплачен ".$response->message."";
			
			exit();

		}

		$order_id = intval($response->reference);
		$order = get_order($order_id);

		if (!$order) {
			echo "Ошибка оплаты заказа ".$response->message."";
			header('Location: http://'.$_SERVER[HTTP_HOST].'/cart/fail/');
		}

		echo $response->message;
		header('Location: http://'.$_SERVER[HTTP_HOST].'/cart/success/?b2p='.$order_id);
		exit();

}




function orderAsPayed($response) {
	global $password;
	// looking for an order
	$order_id = intval($response->reference);
	if ($order_id == 0)
		return false;

	$order = get_order($order_id);
	if (!$order)
		return false;

	// check payment state
	if (($response->type != 'PURCHASE' && $response->type != 'EPAYMENT') || $response->state != 'APPROVED')
		return false;

	// check server signature
	$tmp_response = json_decode(json_encode($response), true);
	unset($tmp_response["signature"]);
	unset($tmp_response["protocol_message"]);

	$signature = base64_encode(md5(implode('', $tmp_response) . $password));
	if ($signature !== $response->signature) {
		// изменить статус на не оплачено $order->update_status('fail', $response->message);
		return false;
	}

	//$order->add_order_note( __('Payment completed.', 'best2pay_woocommerce') );
	//$order->payment_complete();
	// статус оплачено!!!!

	return true;

}