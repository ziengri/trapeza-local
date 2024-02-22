<?php
ini_set('memory_limit', '1000M');
set_time_limit(-1);

use App\modules\Korzilla\Excel\Import\ImportExcel;

require_once dirname(__DIR__, 5)  . '/include_console.php';
require_once $_SERVER['DOCUMENT_ROOT'] . "/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/connect_io.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/modules/default/function.inc.php";

$Catalogue_ID = $Catalogue_ID ?: $_GET['Catalogue_ID'];

$import = new ImportExcel($Catalogue_ID);
return $import->getCatalog();
