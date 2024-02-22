<?php
ini_set('max_execution_time','1000000');
ini_set('memory_limit', '2000M');
set_time_limit(1000000);

// debug verh
if ($_SERVER['REMOTE_ADDR']=='31.13.133.138' && $_SERVER['HTTP_HOST']=='krza.ru') {
	echo "post_max_size: ".ini_get("post_max_size")."<br>";
	echo "upload_max_filesize: ".ini_get("upload_max_filesize")."<br>";
	echo "max_execution_time: ".ini_get("max_execution_time")."<br>";
	echo "memory_limit: ".ini_get("memory_limit")."<br>";
}
if ($_SERVER['HTTP_HOST']=='krza.ru') die;
$v1c = '';
include_once($_SERVER['DOCUMENT_ROOT']."/bc/modules/default/1c_exchange.php");