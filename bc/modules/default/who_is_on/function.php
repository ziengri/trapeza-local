<?php
ini_set('memory_limit', '600M');
set_time_limit(1000000);

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";
global $db;

if (!getIP('office')) die('[{}]');
header('Content-Type: application/json');

$catalogs = $db->get_results("SELECT `login`, `Domain`, `Catalogue_Name`, `Catalogue_ID` FROM Catalogue WHERE Checked = 1", ARRAY_A);
$search = $_POST['textSearch']; //'sberLogin';
$patern = '/"'.$search.'"\s{0,}:\s{0,}"(.*?)"/';
$result = [];

if (!$search) die('[]');
foreach ($catalogs as $catalog) {
    $settingJson = file_get_contents($ROOTDIR . "/a/{$catalog['login']}/settings.ini");
    unset($matches);
    preg_match($patern, $settingJson, $matches);
    if ($matches[1]) $result[] = ['catalog' => $catalog, 'res' => $matches[1]];
}

// var_dump($result);
echo json_encode($result);
