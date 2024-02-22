<?php
ob_start();

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";

global $pathInc, $DOCUMENT_ROOT, $pathInc2, $setting;

$curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
$user_action = 'checkOrderStatus_sberbank';
# user function
$thisFile = $DOCUMENT_ROOT.$pathInc2."/user_function.php";
if ($curCat['customCode'] && file_exists($thisFile)) {
	include_once($thisFile);
} else {
	include_once($MODULE_FOLDER."default/user_function.inc.php");
}

if ($curCat['Catalogue_ID']>0) {

	if($user_action){
		$uf = new userfunction();
		echo $uf->init($user_action);
	}else{
		echo "";
	}

} else {
	exit;
}
