<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
require_once $ROOTDIR . '/bc/modules/default/PaymentMethod/class_payment_method.php';

global $db;

$message = 1944;

$cart = $db->get_row("SELECT * FROM Message2005 WHERE Message_ID = '{$message}'", ARRAY_A);
$PaymentMethod = new PaymentMethod($message, orderArray($cart['orderlist']), orderArray($cart['customf']));
var_dump($PaymentMethod->avangarde());
