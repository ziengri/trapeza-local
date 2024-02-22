<?php
ini_set('memory_limit', '-1');
set_time_limit(0);

use App\modules\bitcat\Cron\Controller;

require_once __DIR__ . '/../../../include_console.php';

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require $ROOTDIR . "/vars.inc.php";
require $ROOTDIR . "/bc/connect_io.php";
require $ROOTDIR . "/bc/modules/default/function.inc.php";

$crontab = new Controller();
$crontab->checkWorkTasks();
$crontab->setWork();