<?
ini_set('memory_limit', '600M');

set_time_limit(1000000);
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";

GLOBAL $db, $pathInc, $pathInc2, $catalogue, $isObjDB, $isObjDB2, $current_catalogue, $nc_core, $field_connect, $setting, $currencyArray;

$mysqli = new mysqli($MYSQL_HOST,$MYSQL_USER,$MYSQL_PASSWORD,$MYSQL_DB_NAME);

//Выводим ошибку соединения
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}

$loginAll = $mysqli->query("select Domain, Mirrors, login from Catalogue ORDER BY Catalogue_ID DESC");


$filelogin2 = $ROOTDIR."/.logins.php";

$file[] = "<? \$x = array(";

while($lg = $loginAll->fetch_assoc()) {
	$file2[] = "'".mb_strtolower($lg['Domain'])."'=>'".$lg['login']."'";
	
	foreach(explode("\r\n",$lg['Mirrors']) as $mirr) {
		if (trim($mirr)!='' && trim($mirr)!=' ') $file2[] = "'".mb_strtolower($mirr)."'=>'".$lg['login']."'";
		
	}
}


$file[] = implode(",",$file2).");";
if (count($file2)>50 && file_put_contents($filelogin2,implode("\n",$file))) echo 'ok'; else echo 'err';