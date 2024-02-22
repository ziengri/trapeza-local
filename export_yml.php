<?
ini_set('memory_limit', '2600M');
set_time_limit(1000000);

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";

global $db, $pathInc, $nc_core, $DOCUMENT_ROOT;

while (ob_get_level() > 0) {
    ob_end_flush();
}

$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));

$classFile = $ROOTDIR."/b/{$current_catalogue['login']}/exportYml.php";
if (file_exists($classFile)) {
    require_once $classFile;
} else if($current_catalogue['Catalogue_ID'] != 955){
    require_once $ROOTDIR."/bc/modules/default/exportYml.php";
}

$exportYml = new ExportYml($pathInc, $current_catalogue['Catalogue_ID'], $db);

$forcibly = $_GET['forcibly'] ? $_GET['forcibly'] : false;
$exportResult = $exportYml->pull($forcibly);

if ($exportResult['success'] == 'error') {
    echo '<br/><b>Ошибка:</b> '.($exportResult['error']);
} else {
    echo 'Выгрузка успешно завершена';
}
//
// $exportYml->deleteAllSubs(); # удаление всех разделов и инфаблоков с сайта
// $exportYml->deleteAllGoods(); # удаление всех товаров с сайта
