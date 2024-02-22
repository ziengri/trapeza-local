<?php
if (isset($argv[1]) && !empty($argv[1])) {
	$paramsString = $argv[1];
	$params = explode('&', $paramsString);

	foreach($params as $param) {
		$paramArr = explode('=', $param);
		$key = $paramArr[0];
		$$key = $paramArr[1];
	}
	unset($key);
	unset($paramArr);
}

$_SERVER['DOCUMENT_ROOT'] = ($_SERVER['DOCUMENT_ROOT'] ?: __DIR__);
$_SERVER['HTTP_HOST'] = (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($HTTP_HOST) ? $HTTP_HOST : 'krza.ru'));
$_SERVER['REMOTE_ADDR'] = (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : explode(' ', $_SERVER['SSH_CLIENT'])[0]);