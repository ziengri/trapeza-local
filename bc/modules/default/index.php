<?php
ob_start();
if ($_GET['test'] == 'test') {
	echo 1;
}
$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)) . (strstr(__FILE__, "/") ? "/" : "\\");
include_once($NETCAT_FOLDER . "vars.inc.php");
require($INCLUDE_FOLDER . "index.php");
// include_once($MODULE_FOLDER."default/user_function.inc.php");

global $pathInc, $DOCUMENT_ROOT, $pathInc2, $setting;

$curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));

# user function
$thisFile = $DOCUMENT_ROOT . $pathInc2 . "/user_function.php";
if ($curCat['customCode'] && file_exists($thisFile)) {
	include_once($thisFile);
} else {
	include_once($MODULE_FOLDER . "default/user_function.inc.php");
}

if ($curCat['Catalogue_ID'] > 0) {

	if ($user_action) {
		try {
			$uf = new userfunction();
			echo $uf->init($user_action);
		} catch (\Exception $e) {
			echo json_encode(['status' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
		}
	} else {
		echo "";
	}

} else {
	exit;
}

?>