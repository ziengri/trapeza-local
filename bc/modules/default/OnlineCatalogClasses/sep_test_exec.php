<?php

ini_set('memory_limit', '600M');
set_time_limit(1000000);

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $pathInc2, $current_catalogue, $catalogue;

# oc - online catalog
$ocNames = ['micado', 'rossko', 'forum_auto','partcom','armtek'];
foreach($ocNames as $className) {
    $seporatedFile = $ROOTDIR.$pathInc2.'/OnlineCatalogClasses/models/'.$className.'_sep.class.php';
    if(file_exists($seporatedFile)) {
        var_dump( $seporatedFile . '  ~sep'); 
        continue;
    }
    var_dump(   __DIR__ . '/models/'.$className.'.class.php' .' ~~ main');
}