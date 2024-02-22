<?php 
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require $ROOTDIR."/vars.inc.php";
require $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
require_once $ROOTDIR."/bc/modules/bitcat/function.inc.php";
GLOBAL $db, $pathInc, $DOCUMENT_ROOT, $catalogue, $current_catalogue, $nc_core;


// получить ID сайта и параметры
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

if (!$catalogue || !$_GET['fname'] || !is_numeric($_GET['ftype'])) $err=1;

$ftype = $_GET['ftype'];

$fname = encodestring($_GET['fname'],2);


// типы файлов
$types = array(1=>'txt',2=>'html',3=>'xml',4=>'ico');

$filepath = $DOCUMENT_ROOT.$pathInc."/".$fname.".".$types[$ftype];

if (file_exists($filepath)) {
	$filetext = @file_get_contents($filepath);

	if ($filetext) {
		if ($ftype==3) { header("Content-Type: text/xml; charset=windows-1251"); header("Pragma: no-cache");}
		if ($ftype==4) { header("Content-Type: image/x-icon; charset=utf-8"); header("Pragma: no-cache");}
		echo $filetext;
	} else {
		$err=1;
	}
} else {
	$err=1;
	echo $filepath;
}

if ($err) header('Location: /404/');