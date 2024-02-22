<?php
# [START]
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? $argv[1] ?? '';
$_SERVER['DOCUMENT_ROOT'] = $_SERVER['DOCUMENT_ROOT'] ?: getRootDir(7);

require_once $_SERVER['DOCUMENT_ROOT']."/vars.inc.php";
require_once $_SERVER['DOCUMENT_ROOT']."/bc/connect_io.php";
include_once($_SERVER['DOCUMENT_ROOT'] . '/autoload.php');

global $nc_core, $current_catalogue, $catalogue;

if (
    !($current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']), true))
    || !isValidCatalogue($current_catalogue)
) {
    die('Не удалось определить сайт');
}

$catalogue = (int) $current_catalogue['Catalogue_ID'];

require_once $_SERVER['DOCUMENT_ROOT']."/bc/modules/default/function.inc.php";

use App\modules\Korzilla\Service\Delivery\Cdek\Cdek;

// print_r($setting);
$cdek = new Cdek();
foreach (getOrders($catalogue) as $row) {
    try {
        $order_info = $cdek->getOrderInfoByImNumber($row['Message_ID']);
    } catch (\Throwable $th) {
        continue;
    }

    $cdek_number = ($order_info['entity'] && $order_info['entity']['cdek_number'])?$order_info['entity']['cdek_number']:''; 


    if(!empty($cdek_number))updateCdekNumber($row,$cdek_number);

    print_r($cdek_number);echo "<br>";
}

function getOrders($catalogue): array
{   
    global $nc_core;

    $data = getOrdersTimeLimit(); 

    $sql = "SELECT `Message_ID`, `cdek_number`
            FROM `Message2005` 
            WHERE `Catalogue_ID` = '$catalogue' AND
            (`cdek_number` = '' OR `cdek_number` IS NULL)
                AND `Created` > '$data' ";

    return $nc_core->db->get_results($sql, ARRAY_A) ?: [];
}

function getOrdersTimeLimit(): string
{   

    return (new DateTime())->format('Y-m-d')." 00:00:00";
}


    function updateCdekNumber($order, $cdek_number)
{
    global $nc_core;

    $sql = "UPDATE `Message2005` SET `cdek_number` = '$cdek_number' WHERE `Message_ID` = {$order['Message_ID']}";
    $nc_core->db->query($sql);
}



/**
 * Получить корневую директорию
 * 
 * @author Олег Хрулёв
 * 
 * @param int $deepLevel уровень глубены относительно корня проекта
 * 
 * @return string 
 */
function getRootDir(int $deepLevel = 0): string
{
    $pathArr = explode('/', __DIR__);
    return implode('/', array_slice($pathArr, 0, count($pathArr) - $deepLevel));
}


/**
 * Обязательно к использованию из-за особенностей работы метода \nc_Catalogue::get_by_host_name
 * 
 * @author Олег Хрулёв
 * 
 * @param array $current_catalogue сайт
 * 
 * @return bool
 */
function isValidCatalogue($current_catalogue): bool
{
    $domains = [$current_catalogue['Domain'] ?: 'nodomen_qwertyxyz'];
    foreach (explode("\n", $current_catalogue['Mirrors']) as $mirror) {
        $domains[] = strtolower(trim(str_replace(array('http://', 'https://', '/'), '', $mirror))) ?: 'nodomen_qwertyxyz';
    }
    return in_array($_SERVER['HTTP_HOST'], $domains, true);
}