<?

ini_set('memory_limit', '800M');

set_time_limit(1000000);
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

while (ob_get_level() > 0) {
    ob_end_flush();
}

foreach($db->get_results("select Subdivision_ID from Sub_Class where Catalogue_ID = '{$catalogue}' GROUP by Subdivision_ID",ARRAY_A) as $sub) {
	$isSub = $db->get_var("select Subdivision_ID from Subdivision where Subdivision_ID = '".$sub['Subdivision_ID']."'");
	if ($isSub) {
		echo "\r\n";
	} else {
		//$parrent = $db->get_var("select Hidden_URL, EnglishName from Subdivision where Parrent_Sub_ID = '".$sub['Subdivision_ID']."'");
		
		$delSub[] = $sub['Subdivision_ID'];
	}
	flush();
	ob_flush();
}

if (count($delSub)>0) {
	$rootCatalog = $db->get_row("select a.Subdivision_ID as sub, b.Sub_Class_ID as cc from Subdivision as a, Sub_Class as b where a.Subdivision_ID = b.Subdivision_ID AND a.Hidden_URL = '/catalog/' AND a.Catalogue_ID = '{$catalogue}'", ARRAY_A);
	
	echo "Нужно восстановить разделы ".print_r($delSub,1);
	echo print_r($rootCatalog,1)."- ".$catalogue;
	$sql = "update Message2001 set Subdivision_ID = '".$rootCatalog['sub']."', Sub_Class_ID = '".$rootCatalog['cc']."' where Subdivision_ID IN (".implode(",",$delSub).")";
	//$db->query($sql);
	echo "\n<br>".$sql."\n";
	
	$sql2 = "delete from Sub_Class where Subdivision_ID IN (".implode(",",$delSub).")";
	//$db->query($sql);
	echo "\n<br>".$sql2."\n";
} else {
	echo "Все хорошо";
}