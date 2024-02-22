<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
require_once $ROOTDIR . '/bc/modules/default/OnlineCatalogClasses/models/search.class.php';

global $pathInc2, $current_catalogue, $catalogue, $settings;

# oc - online catalog
$ocNames = ['micado', 'rossko', 'forum_auto','partcom','armtek', 'profitLeague'];
foreach($ocNames as $className) {
    $seporatedFile = $ROOTDIR.$pathInc2.'/OnlineCatalogClasses/models/'.$className.'_sep.class.php';
    if(file_exists($seporatedFile)) {
        require_once $seporatedFile;
        continue;
    }
    require_once $ROOTDIR . '/bc/modules/default/OnlineCatalogClasses/models/'.$className.'.class.php';
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

    switch ($nameModule) {
        case 'armtek':
            $items = Armtek::find($find);
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
    echo "<pre>";
    var_dump($items);
    echo 'success ' . count($items);
} catch (\Exception $e) {
    echo $e->getMessage();
}
