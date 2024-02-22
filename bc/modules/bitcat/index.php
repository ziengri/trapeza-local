<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: application/json");
header("Accept-Charset: utf-8");

$module_keyword = "bitcat";
/* $Id: index.php 5266 2011-09-01 14:50:02Z gaika $ */

$NETCAT_FOLDER = join(strstr(__FILE__, "/") ? "/" : "\\", array_slice(preg_split("/[\/\\\]+/", __FILE__), 0, -4)).( strstr(__FILE__, "/") ? "/" : "\\" );
include_once $NETCAT_FOLDER."vars.inc.php";
require_once $ROOT_FOLDER."connect_io.php";

require_once $ADMIN_FOLDER."function.inc.php";

require_once $ADMIN_FOLDER."subdivision/subclass.inc.php";
require_once $INCLUDE_FOLDER."s_files.inc.php";
require_once $INCLUDE_FOLDER."s_common.inc.php";
require_once $ADMIN_FOLDER."subdivision/function.inc.php";
require_once $MODULE_FOLDER.'default/function.inc.php';
require_once $MODULE_FOLDER.$module_keyword."/function_adminorders.inc.php";

global $inside_admin, $db;
$inside_admin = false;

$nc_core->modules->load_env();

Authorize();

set_time_limit(3600);


$curCat = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));

if (empty($curCat['Catalogue_ID']) || !$perm->isCatalogueAdmin($curCat['Catalogue_ID'])) {
	die('Permission denied');
}

switch (true) {
	case !empty($bc_action):
		$controller = new bc();

		echo $controller->init_bc($bc_action);
		break;
	case !empty($bc_adminorders):
		$controller = new adminorders();
		echo $controller->init($bc_adminorders);
		break;
	case !empty($bc_copy_action):
		$controller = new App\modules\bitcat\Copy\Controller();
		if (method_exists($controller, $bc_copy_action)) {
			$db->query("UPDATE Message2001 SET Keyword = NULL WHERE Keyword = '' AND Catalogue_ID = {$curCat['Catalogue_ID']}");
			echo $controller->$bc_copy_action();
		} else {
			# неизвестный метод 
			echo 'неизвестный метод '.$bc_copy_action;
		}
		break;
}
