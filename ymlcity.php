<?
header("Content-Type: text/xml; charset=utf-8");
$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";

global $db, $pathInc, $pathInc2, $catalogue, $HTTP_HOST;

if (isset($_GET['n']) && is_numeric($_GET['n']) && file_exists($ROOTDIR . $pathInc . "/yml/import/yml{$_GET['n']}.xml")) {
    $basename = "yml{$_GET['n']}";
    if ($_GET['c'])  $basename . "_{$_GET['c']}";
    $fullpath = $ROOTDIR . $pathInc . "/yml/import/{$basename}.xml";
} else {
    $basename = (file_exists($ROOTDIR . $pathInc . '/yml_turbo.xml') ? 'yml_turbo' : 'yml');
    if (is_numeric($_GET['n'])) $basename = $basename . "_" . $_GET['n'];
    $fullpath = $ROOTDIR . $pathInc . '/' . $basename . '.xml';
}


$domenArr = explode(".", $HTTP_HOST);

if (count($domenArr) == 2) {
    echo file_get_contents($fullpath);
    exit;
};

$domen = $domenArr[1] . "." . $domenArr[2];
$dopdomen = $domenArr[0];
$ymlacheFld = $ROOTDIR . $pathInc . '/ymlcache/';
$ymlcache = $ymlacheFld . $dopdomen . "_" . $basename;


if (validateDateCreateXml($ymlcache,$fullpath)) { // верем из кеша если есть отличие в создание файлов
    echo file_get_contents($ymlcache);
} else { // кеша нет или он устарел, делаем новый файл
    $xml = file_get_contents($fullpath);
    $xml = str_replace("/" . $domen, "/" . $HTTP_HOST, $xml);
    if (file_put_contents($ymlcache, $xml)) echo $xml;
    else @mkdir($ymlacheFld);
}


function validateDateCreateXml($ymlcache, $fullpath) {
    if (!file_exists($ymlcache)) return false;

    $reader = new \XMLReader();
    $reader->open($ymlcache);
    $timeCreateCache = '';
    while ($reader->read()) {
        if ($reader->nodeType == XMLReader::ELEMENT) {
            if ($reader->localName == 'yml_catalog') {
                $timeCreateCache = $reader->getAttribute('date');
                break;
            }
        }
    }
    $reader->close();

    $reader->open($fullpath);
    $timeCreate = '';
    while ($reader->read()) {
        if ($reader->nodeType == XMLReader::ELEMENT) {
            if ($reader->localName == 'yml_catalog') {
                $timeCreate = $reader->getAttribute('date');
                break;
            }
        }
    }
    $reader->close();

    return $timeCreateCache == $timeCreate;
}