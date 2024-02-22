<?php
ini_set('memory_limit', '2000M');
set_time_limit(0);

use App\modules\Korzilla\YML\ImportYML;
use App\modules\Korzilla\YML\Controller;
use Custom\YML\ImportYMLCustom;

require_once __DIR__ . '/../../../include_console.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/connect_io.php";

$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.","",$_SERVER['HTTP_HOST']));

require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/modules/default/function.inc.php";


$action = $action ?: $_GET['action'];
switch ($action) {
    case 'import':
        $message_id = $message_id ?: $_GET['message_id'];
        if (empty($message_id)) throw new Exception('Нет id выгрузки', 404);
        if (class_exists('Custom\YML\ImportYMLCustom')) {
            $importYML = new ImportYMLCustom($message_id);
        } else {
            $importYML = new ImportYML($message_id);
        }
        break;
    case 'status':
        $message_id = $message_id ?: $_GET['message_id'] += 0;
        $importYML = new Controller();
        echo $importYML->getStatusImport($message_id);
        break;
}