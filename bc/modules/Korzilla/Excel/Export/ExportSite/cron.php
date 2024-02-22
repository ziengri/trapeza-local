<?php

ini_set('memory_limit', '1000M');
set_time_limit(-1);

use App\modules\Korzilla\Excel\Export\ExportSite\Model\Export;
use App\modules\Korzilla\Excel\ProcessLog;

require_once dirname(__DIR__, 6)  . '/include_console.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/connect_io.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/modules/default/function.inc.php";

$Catalogue_ID = $Catalogue_ID ?: $_GET['Catalogue_ID'];

try {
    $Export = new Export($Catalogue_ID);
    $Export->parsing();
} catch (\Exception $e) {
    $ProcessLog = new ProcessLog($Export->pathProcess);
    $ProcessLog->setError($e->getMessage())
                ->setStatus($Export::STATUS_ERROR)
                ->save();
}

