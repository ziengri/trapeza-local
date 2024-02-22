<?php

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";
global $pathInc, $pathInc2;

$filePath = $ROOTDIR . $pathInc . "/sitemap{$path}.xml";


$domainArr = explode(".", $_SERVER['HTTP_HOST']);
$platforms = array("krzl.ru","krzi.ru","kzll.ru");
if (count($domainArr) == 3 && !in_array($domainArr[1], $platforms)) {
    $mainDomain = "{$domainArr[1]}.{$domainArr[2]}";
}
$urlSite = "http://" . ($mainDomain ? $mainDomain : $_SERVER['HTTP_HOST']);
$updatePath = "/bc/modules/bitcat/sitemap.php";
$customCodeUpdatePath = $pathInc2 . '/sitemap.php'; 


if (!file_exists($filePath) || (file_exists($filePath) && date("d.m.Y", filemtime($filePath)) != date("d.m.Y")) || $_GET['forceRefresh'] == 1) {
    if (file_exists($ROOTDIR . $customCodeUpdatePath)) $updatePath = $customCodeUpdatePath;
    
    $update = @file_get_contents($urlSite . $updatePath);
}

if (!file_exists($filePath)) die;

$xml = @file_get_contents($filePath);
if ($mainDomain) $xml = str_replace("//" . $mainDomain, "//" . $_SERVER['HTTP_HOST'], $xml);

if ($xml) {
    header("Content-Type: text/xml; charset=UTF-8");
    echo $xml;
}
