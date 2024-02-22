<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
require_once $ROOTDIR."/bc/modules/default/PaymentMethod/Cart/cart.class.php";
require_once $ROOTDIR."/bc/modules/default/mailAsisit.class.php";

global $db, $current_catalogue, $setting, $DOCUMENT_ROOT, $pathInc, $cityname;

updateStatusOplatyCustom(1825, 2,true);
