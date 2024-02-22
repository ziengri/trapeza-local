<?php

use App\modules\Korzilla\CatalogItem\Tab\Controllers\SettingController;

require_once $_SERVER['DOCUMENT_ROOT'] . "/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/connect_io.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/modules/default/function.inc.php";

global $pathInc;

$controllerTabSetting = new SettingController($_SERVER['DOCUMENT_ROOT'] . $pathInc);

return $controllerTabSetting->getView();
