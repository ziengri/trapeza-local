<?php

ini_set('memory_limit', '600M');
set_time_limit(1000000);

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
require_once __DIR__ . '/models/search.class.php';

// require_once __DIR__ . '/models/micado.class.php';
// require_once __DIR__ . '/models/rossko.class.php';
// require_once __DIR__ . '/models/forum_auto.class.php';
// require_once __DIR__ . '/models/partcom.class.php';
// require_once __DIR__ . '/models/armtek.class.php';

global $pathInc2, $current_catalogue, $catalogue;

# oc - online catalog
$ocNames = ['micado', 'rossko', 'forum_auto','partcom','armtek', 'profitLeague'];
foreach($ocNames as $className) {
    $seporatedFile = $ROOTDIR.$pathInc2.'/OnlineCatalogClasses/models/'.$className.'_sep.class.php';
    if(file_exists($seporatedFile)) {
        require_once $seporatedFile;
        continue;
    }
    require_once __DIR__ . '/models/'.$className.'.class.php';
}
# 

if (!$current_catalogue) {
    $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    if (!$catalogue) {
        $catalogue = $current_catalogue['Catalogue_ID'];
    }
}
$nameModule = $_GET['module'];
$find = $_GET['find'];

try {
    $search = new Search($find, $nameModule);

    if (!$search->isCacheFind()) {
        switch ($nameModule) {
            case 'mikado':
                $micado = new Micado();
                $items = $micado->getSearchResult($find);
                break;
            case 'partcom':
                $partcom = new Partcom();
                $items = $partcom->getSearchResult($find);
                break;
            case 'rossko':
                $rossko = new Rossko();
                $items = $rossko->getSearchResult($find);
                break;
            case 'forum_auto':
                $forumAuto = new ForumAuto();
                $items = $forumAuto->getSearchResult($find);
                break;
            case 'armtek':
                $items = Armtek::find($find);
                break;
            case 'profitLeague':
                if (!$settings) {
                    $settings = getSettings();
                }
                $cc = (int) $nc_core->db->get_var("SELECT `Sub_Class_ID` 
                                                    FROM `Sub_Class` 
                                                    WHERE `Subdivision_ID` = {$settings['profitLeague_save_sub']} 
                                                        AND `Class_ID` = 2001");
                
                require_once $ROOTDIR . '/bc/modules/default/OnlineCatalogClasses/handlers/Keyword.php';
                
                $profitLeague = new ProfitLeague(
                    $settings['profitLeague_secret_key'],
                    $settings['profitLeague_save_sub'],
                    $cc,
                    $catalogue,
                    $settings['profitLeague_markup'] ?? 0,
                    new Keyword
                );
                
                $items = $profitLeague->getSearchResult($find);
                break;
        }
        $search->setCache();
        if (!empty($items) && is_array($items)) {
            $search->saveItems($items);
        }
        echo 'success ' . count($items);
    } else {
        echo 'cache';
    }
} catch (\Exception $e) {
    file_put_contents('/var/www/krza/data/www/krza.ru/a/ilsur/forumauto.log', print_r($e->getMessage(), 1));
    echo $e->getMessage();
}
