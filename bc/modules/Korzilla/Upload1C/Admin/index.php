<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

use App\modules\Korzilla\Upload1C\Admin\Controller\ControllerSettingsFrom;
use App\modules\Korzilla\Upload1C\Admin\Models\ModelSettingsFrom;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $db, $pathInc, $pathInc2, $catalogue, $isObjDB, $isObjDB2, $current_catalogue, $nc_core, $field_connect, $setting, $currencyArray;

if (!$current_catalogue) {
    $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

$ControllerSettingsFrom = new ControllerSettingsFrom((new ModelSettingsFrom()));
switch ($_GET['action']) {
    case 'get':
        $result = $ControllerSettingsFrom->getForm();
        break;
    case 'update_autoload':
        $result = json_encode($ControllerSettingsFrom->updateAutoload($_POST));
        break;
    case 'save':
        $result = '';
        break;
    default:
        $result = '';
        break;
}

echo $result;
