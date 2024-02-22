<?php
require_once dirname(__DIR__, 5)  . '/include_console.php';

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
switch ($action) {
    case 'import':
        App\modules\Korzilla\Excel\Export\Controller::import($Catalogue_ID, $messageID);
        break;
    case 'process':
        echo App\modules\Korzilla\Excel\Export\Controller::process($Catalogue_ID, $messageID);
        break;
    default:
        return [];
        break;
}
