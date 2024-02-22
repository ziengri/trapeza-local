<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
require_once(__DIR__ . '/OrderTurbo.class.php');
require_once(__DIR__ . '/ExportTurbo.class.php');

use turbo\ExportTurbo;
use turbo\OrderTurbo;

if ($_GET['order']) {
    $order = new OrderTurbo();
    $order->getAction($_GET['order']);
}

if ($_GET['token'] != '') {
    $item = new ExportTurbo($_GET['token']);

    header("Content-Type: text/xml");
    echo $item->setFile($_GET['token']);
}
