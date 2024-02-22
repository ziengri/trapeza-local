<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: application/json");
header("Accept-Charset: utf-8");

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -5)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once $NETCAT_FOLDER."vars.inc.php";
require_once $ROOT_FOLDER."connect_io.php";

require_once $ADMIN_FOLDER."function.inc.php";

require_once $ADMIN_FOLDER."subdivision/subclass.inc.php";
require_once $INCLUDE_FOLDER."s_files.inc.php";
require_once $INCLUDE_FOLDER."s_common.inc.php";
require_once $ADMIN_FOLDER."subdivision/function.inc.php";
require_once "./function.inc.php";

$nc_core->modules->load_env();

Authorize();

set_time_limit(3600);

$curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
if ($curCat['Catalogue_ID']>0 && $perm->isCatalogueAdmin($curCat['Catalogue_ID'])) {

	if($action){

		$separate = new separate();
		echo $separate->init($action);

	}else{
		echo "";
	}

} else {
	exit;
}




?>