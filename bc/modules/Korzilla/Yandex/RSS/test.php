<?php

use App\modules\Korzilla\Yandex\RSS\Creater;
use App\modules\Korzilla\Yandex\RSS\Object182;
use App\modules\Korzilla\Yandex\RSS\Object2003;
use App\modules\Korzilla\Yandex\RSS\Object2021;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR."/vars.inc.php";
require_once $ROOTDIR."/bc/connect_io.php";
require_once $ROOTDIR."/bc/modules/default/function.inc.php";

$current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
$domen = ($current_catalogue['https'] ? 'https://' : 'http://') . $current_catalogue['Domain'];
$Obj182 = new Object182($db, $current_catalogue['Catalogue_ID'], $domen);
$Obj2003 = new Object2003($db, $current_catalogue['Catalogue_ID'], $domen);
$Obj2021 = new Object2021($db, $current_catalogue['Catalogue_ID'], $domen);

$items = array_merge($Obj2021->getItems(), $Obj182->getItems(), $Obj2003->getItems());

$rss = new Creater([
    'title' => $current_catalogue['Catalogue_Name'],
    'link' => $domen,
    'items' => $items
]);

echo $rss->create();