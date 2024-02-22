<?php 
ini_set('memory_limit', '400M');

set_time_limit(1000000);
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
GLOBAL $db, $pathInc, $pathInc2, $catalogue, $current_catalogue, $nc_core, $field_connect, $setting, $login;


while (ob_get_level() > 0) {
    ob_end_flush();
}

/*
if (!$current_catalogue) {
	$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));
	if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}*/

if ($db->query("repair table Session")) echo "ok";
if ($db->query("repair table Subdivision")) echo " ok";