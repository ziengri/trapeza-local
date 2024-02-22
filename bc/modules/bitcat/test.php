<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $setting;
$blok = getSettingsManifestBlock('yml_turbo_setting', 'yml_turbo');
var_dump($blok);

