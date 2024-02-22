<?php

use App\modules\Korzilla\CatalogItem\Tab\Controllers\SettingController;

require_once $_SERVER['DOCUMENT_ROOT'] . "/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/connect_io.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/bc/modules/default/function.inc.php";

global $pathInc;

$Controller = new SettingController($_SERVER['DOCUMENT_ROOT'] . $pathInc);

switch ($_GET['action']) {
    case 'get_modal_setting':
        echo $Controller->getModalSettingOneTab($_GET['id']);
        break;
    case 'save_setting_tab':
        echo json_encode($Controller->save($_POST));
        break;
    case 'drop_tab':
        echo json_encode($Controller->delete($_GET['id']));
        break;
    case 'add_tab':
        echo $Controller->getModalSettingNewTab();
        break;
    case 'dragged_tab':
        echo json_encode($Controller->draggedTab($_POST['serialize']));
        break;
    case 'loadsetclass':
        echo $Controller->loadSetClass((int) $_GET['typecont'], (int) $_GET['subid'], []);
        break;
}
