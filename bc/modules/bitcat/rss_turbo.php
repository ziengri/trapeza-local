<?php

use App\modules\Korzilla\Yandex\RSS\Creater;
use App\modules\Korzilla\Yandex\RSS\Object182;
use App\modules\Korzilla\Yandex\RSS\Object2003;
use App\modules\Korzilla\Yandex\RSS\Object2021;
use \Exception;

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $db;

try {
    $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    $fileLink = "/a/{$current_catalogue['login']}/rss_turbo.xml";
    $filePath = $ROOTDIR . $fileLink;
    $domen = ($current_catalogue['https'] ? 'https://' : 'http://') . $current_catalogue['Domain'];

    $Obj182 = new Object182($db, $current_catalogue['Catalogue_ID'], $domen);
    $Obj2003 = new Object2003($db, $current_catalogue['Catalogue_ID'], $domen);
    $Obj2021 = new Object2021($db, $current_catalogue['Catalogue_ID'], $domen);

    // Получение всех последних элементов в колчиестве 90 штук
    $items = array_merge(
        array_slice(array_reverse($Obj2021->getItems()), 0, 80),
        array_slice(array_reverse($Obj182->getItems()), 0, 80),
        array_slice(array_reverse($Obj2003->getItems()), 0, 80)
    );

    if (empty($items))
        throw new Exception("Нет элементов", 1);

    $rss = new Creater([
        'title' => $current_catalogue['Catalogue_Name'],
        'link' => $domen,
        'items' => $items
    ]);

    if (!file_put_contents($filePath, $rss->create())) {
        throw new Exception("Не удалось записать файл!!!", 1);
    } else {
        echo "Ссылка на файл <a href='{$fileLink}' target='_blank'>rss_turbo.xml</a>";
    }
} catch (\Exception $error) {
    echo $error->getMessage();
}
?>