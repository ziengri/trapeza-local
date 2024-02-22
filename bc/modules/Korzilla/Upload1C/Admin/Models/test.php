<?php

use App\modules\Korzilla\Upload1C\Exchange\Models\CheckForUpdates;


$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

$CheckForUpdates = new CheckForUpdates(1121, 1);
$isUpdate = $CheckForUpdates->checkFolder();
var_dump($isUpdate);