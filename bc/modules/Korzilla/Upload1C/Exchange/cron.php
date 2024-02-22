<?php

use App\modules\Korzilla\Upload1C\Exchange\Models\CheckForUpdates;
use App\modules\bitcat\Cron\Controller as Cron;

require_once __DIR__ . '/../../../../../include_console.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/connect_io.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/modules/default/function.inc.php";

global $pathInc2;

$CheckForUpdates = new CheckForUpdates($catalogueID, $version);
$isUpdate = $CheckForUpdates->checkFolder();

if (!$isUpdate) exit;

$CheckForUpdates->disabledCheckCron((new Cron()));

// ! Часть с запуском скрипта требует доработки
$action = 'import';
$v1c = $version;
unset($argv);

if (file_exists($_SERVER['DOCUMENT_ROOT'] . $pathInc2 . '/export_1c.php')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . $pathInc2 . '/export_1c.php';
} else require_once $_SERVER['DOCUMENT_ROOT'] . '/export_1c.php';
