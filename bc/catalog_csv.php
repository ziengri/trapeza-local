<?php

ini_set('memory_limit', '-1');
set_time_limit(0);

use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\IOFactory;

define('NOT_ROOT_GROUP', 1);

if ($argv[1]) {
	$_SERVER['HTTP_HOST'] = $argv[1];
	$_SERVER['DOCUMENT_ROOT'] = '/var/www/krza/data/www/krza.ru';
}

$ROOTDIR = $_SERVER['DOCUMENT_ROOT'];

require_once $ROOTDIR . "/vars.inc.php";
require_once $ROOTDIR . "/bc/connect_io.php";
require_once $ROOTDIR . "/bc/modules/default/function.inc.php";

global $db, $patdInc, $catalogue, $current_catalogue, $nc_core, $field_connect, $pathInc2, $mode_import, $setting;

if (!$current_catalogue) {
    $current_catalogue = $nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
    if (!$catalogue) $catalogue = $current_catalogue['Catalogue_ID'];
}

if (isset($adm)) echo $catalogue * 1271 - 14;
if ($key != $catalogue * 1271 - 14 && $_SERVER['REMOTE_ADDR'] != '31.13.133.138') die;
while (ob_get_level() > 0) {
    ob_end_flush();
}
$mode_import = $GET['mode'] ?: NOT_ROOT_GROUP;
$file = $ROOTDIR . '/a/' . $current_catalogue['login'] . '/catalog.csv';
$group_field_white = [
    'Subdivision_Name' => 'Наименовния раздела',
    'AlterTitle' => 'Альтернативное название раздела (h1)',
    'subdir' => 'Показ подразделов этого раздела',
    'TitleObj' => 'Заголовок карточки объекта (Title)',
    'TitleImg' => 'Альтернативный текст для изображений (Alt)',
    'Title2' => 'Заголовок раздела (Title)',
    'KeywordsObj' => 'Ключевые слова карточки объекта',
    'Keywords2' => 'Шаблон ключевых слов страницы (Keywords)',
    'DescriptionObj' => 'Описание карточки объекта',
    'Description2' => 'Шаблон описания страницы (Description)',
    'imgtoall' => 'Использовать изображение для всех товаров раздела',
    'inMarket' => 'Выгружать в Яндекс.Маркет',
    'text' => 'Описание в разделе',
    'txttoall' => 'Использовать текст для всех товаров раздела',
    'text2' => 'Описание в разделе (снизу)',
    'find' => 'Выбор товаров по фразе',
    'noleftcol' => 'Скрывать левую колонку (зона 1)',
    'blank' => 'Открывать в новом окне',
    'citytarget' => 'Города',
    'catfields' => 'Колонки в каталоге (таблица)',
    'descr' => 'Краткое описание',
    'filtermenu' => 'Подразделы как фильтр',
    'ajaxload' => 'Плавная подгрузка данных',
    'seotext' => 'Шаблон seo-текста в карточке товара',
    'outallitem' => 'Вывод товаров из подразделов',
    'instock' => 'Товары только в наличии',
    'allGoods' => 'Вывести все товары с сайта',
    'defaultOrder' => 'сортировка по умолчанию',
    'seoTextBottom' => 'Сео текст под контентом',
    'sub_find' => 'В каких разделах искать',
    'var1' => 'Параметр 1',
    'var2' => 'Параметр 2',
    'var3' => 'Параметр 3',
    'var4' => 'Параметр 4',
    'var5' => 'Параметр 5',
    'var6' => 'Параметр 6',
    'var7' => 'Параметр 7',
    'var8' => 'Параметр 8',
    'var9' => 'Параметр 9',
    'var10' => 'Параметр 10',
    'var11' => 'Параметр 11',
    'var12' => 'Параметр 12',
    'var13' => 'Параметр 12',
    'var14' => 'Параметр 14',
    'var15' => 'Параметр 15',
    'AlterTitle2' => 'Шаблон заголовка h1',
    'AlterTitleObj' => 'Заголовок H1 карточки объекта',
    'tags_links' => 'тэги',
    'tags_links_bottom' => 'Тэги снизу',
    'no_index_seo' => 'Не индексировать раздел',
    'strictFind' => 'Строгий поиск по фразе',
    'view_obj_by_param' => 'Показывать объекты по выбранным параметрам',
    'rss_turbo_yandex' => 'RSS для контентных турбо станиц',
    'sub_lang' => 'Язык вывода',
    'seoTextBottom2' => 'SEO текст снизу страницы для под. разделов',
];

$item_field_white = [
    'name' => 'Название',
    'text' => 'Полное описание',
    'vendor' => 'Производитель',
    'price' => 'Цена',
    'price2' => 'Цена 2',
    'price3' => 'Цена 3',
    'price4' => 'Цена 4',
    'firstprice' => 'Это нижняя граница цены товара (от)',
    'dogovor' => 'Цена договорная (число не показывается)',
    'torg' => 'Возможен торг',
    'nocart' => 'Запретить добавлять товар в корзину',
    'noorder' => 'Запретить заказывать товар',
    'variable' => 'Варианты товара',
    'buyvariable' => 'Обязательный выбор варианта товара',
    'stock' => 'Наличие на складе (шт.)',
    'stock2' => 'Наличие на складе №2 (шт.)',
    'stock3' => 'Наличие на складе №3 (шт.)',
    'stock4' => 'Наличие на складе №4 (шт.)',
    'notmarkup' => 'Не учитывать общую наценку',
    'discont' => 'Скидка на товар (в %)',
    'pricediscont' => 'или цена со скидкой',
    'disconttime' => 'Скидка действует до',
    'art' => 'Артикул',
    'art2' => 'Артикул или код',
    'artnull' => 'Эталонные артикулы',
    'code' => 'Код',
    'ves' => 'Вес',
    'edizm' => 'Единица измерения',
    'analog' => 'Список артикулов аналогичных товаров (по одному в строке)',
    'spec' => 'Спецпредложение',
    'extlink' => 'Внешняя ссылка',
    'buywith' => 'C этим товаром покупают (артикулы по одному в строке)',
    'itemlabel' => 'Лейбл на товаре',
    'variablename' => 'Название вариантов',
    'variablenameSide' => 'Вариант основного товара',
    'colors' => 'Цвета',
    'buycolors' => 'Обязательный выбор цвета товара',
    'new' => 'Новинки',
    'action' => 'Хит продаж',
    'pricecity' => 'Цены по городам',
    'citytarget' => 'Товар доступен только в городах (по-умолчанию - везде)',
    'capacity' => 'Объем',
    'sizes_item' => 'Размер',
    'height' => 'Высота',
    'width' => 'Ширина',
    'length' => 'длина',
    'depth' => 'Глубина',
    'oneitem' => 'Один товар в разделе (показать сразу всю информацию)',
    'descr' => 'Краткое описание в списке товаров',
    'photourl' => 'Ссылка на сторонний сайт',
    'tags' => 'Тэги',
    'var1' => 'Параметр 1',
    'var2' => 'Параметр 2',
    'var3' => 'Параметр 3',
    'var4' => 'Параметр 4',
    'var5' => 'Параметр 5',
    'var6' => 'Параметр 6',
    'var7' => 'Параметр 7',
    'var8' => 'Параметр 8',
    'var9' => 'Параметр 9',
    'var10' => 'Параметр 10',
    'var11' => 'Параметр 11',
    'var12' => 'Параметр 12',
    'var13' => 'Параметр 13',
    'var14' => 'Параметр 14',
    'var15' => 'Параметр 15',
    'timer' => 'Таймер обратного отсчета',
    'Subdivision_IDS' => 'Доп. разделы в которых показывать товар (номера через запятую)',
    'h1' => 'Альтернативное название H1',
    'currency' => 'Валюта в прайсе',
    'text2' => 'Характеристики',
    'outItems' => 'Вывод объектов (портфолио, фотогалерея)',
    'otherItem' => 'Вариант блока "Вам может также понравиться"',
    'lang' => 'Язык вывода',
];

$params_list = array_reduce($setting['lists_params'], function ($carry, $param)
{
    if (!empty($param['name'])) {
        $carry[$param['keyword']] = $param['name'];
    }
    return $carry;
}, []);

$groupsDB = $db->get_results(
    "SELECT 
        sub.*
    FROM 
        Subdivision as sub,
        Sub_Class as cc
    WHERE 
        sub.Catalogue_ID = '{$catalogue}'
        AND sub.systemsub != 1
        AND sub.nosettings != 1
        AND sub.Subdivision_ID = cc.Subdivision_ID
        AND cc.Class_ID = 2001
    ORDER BY sub.Hidden_URL DESC",
    ARRAY_A
);

$groups = [];
foreach ($groupsDB as $groupDB) $groups[$groupDB['Subdivision_ID']] = $groupDB;
unset($groupDB, $groupsDB);

foreach ($groups as $id => $group) {
    if (isset($groups[$group['Parent_Sub_ID']])) {
        $groups[$group['Parent_Sub_ID']]['childs'][$id] = $groups[$id];
        unset($groups[$id]);
    }
}

$groups = preparationGroup($groups, $group_field_white);

$group_field_white_key = array_keys($group_field_white);
$item_field_white_key = array_keys($item_field_white);

$total_cols_param = 0;
$fp = fopen($file, 'w');
function fputcsv_eol($fp, $array, $eol) {
    fputcsv($fp, $array, ';');
    if("\n" != $eol && 0 === fseek($fp, -1, SEEK_CUR)) {
      fwrite($fp, $eol);
    }
}

foreach ($groups as $group_id => $group) {
    $groupItems = $db->get_results("SELECT * FROM Message2001 WHERE Subdivision_ID = {$group_id} AND Catalogue_ID = '{$catalogue}'", ARRAY_A);
    $group_line = [];
    foreach ($group_field_white_key as $col => $field) {
        $group_line[] = $group[$field];
    }
    fputcsv_eol($fp, $group_line, "\r\n");
    echo "c- ";
    flush();
    $row++;
    foreach ($groupItems as $item) {
        $item_line = [''];
        foreach ($item_field_white_key as $col => $field) {
            $item_line[] = $item[$field];
        }
        if (!empty($item['params'])) {
            $params = array_map(function($param_row) {
                $param_cols = explode('||', $param_row);
                return ['key' => $param_cols[0], 'value' => trim( $param_cols[1], '|')];
            }, array_filter(explode("\r\n", $item['params'])));
            if ($total_cols_param < count($params)) $total_cols_param = count($params) - 1;
            foreach ($params as $param) {
                if (isset($params_list[$param['key']])) {
                    $item_line[] = $params_list[$param['key']];
                    $item_line[] = $param['value'];
                }
            }
        }
        fputcsv_eol($fp, $item_line, "\r\n");
        $row++;
        echo "i- ";
        flush();
        ob_flush();
    }
}
fclose($fp);

for ($i=0; $i <= $total_cols_param; $i++) { 
    $item_field_white["paramName{$i}"] = 'Наименования параметра';
    $item_field_white["paramValue{$i}"] = 'Значения параметра';
}
$item_field_white_key = array_keys($item_field_white);

$total_count_cols = count($group_field_white_key) > count($item_field_white_key) ? count($group_field_white_key) : count($item_field_white_key);
$header = $item_field = $group_field = [];


for ($i=0; $i <= $total_count_cols; $i++) { 
    $group_head = $group_field_white_key[$i];
    $item_head = $item_field_white_key[$i - 1];
    $header[] = implode('/', array_filter([$group_field_white[$group_head], $item_field_white[$item_head]]));
    $item_field[] = $item_head;
    $group_field[] = $group_head;
}
$data = file_get_contents($file);
$fp = fopen($file, 'w');
fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
fputcsv_eol($fp, $header, "\r\n");
fclose($fp);

// file_put_contents($file, $data, FILE_APPEND);


var_dump(implode(',', $item_field));
var_dump(implode(',', $group_field));

function preparationGroup($groups, $group_field_white, $level = 0)
{
    global $mode_import;
    $result = [];
    array_multisort(array_column($groups, 'Priority'), SORT_ASC, $groups);

    foreach ($groups as $group) {
        $preparatData = [];
        foreach ($group as $field => $value) {
            if (isset($group_field_white[$field])) {
                switch ($field) {
                    case 'Subdivision_Name':
                        $value = $level . '. ' . $value;
                        break;
                }
                $preparatData[$field] = $value;
            }
        }
        if ($level === 0 && $mode_import === NOT_ROOT_GROUP) $preparatData = [];

        if (!empty($preparatData))  $result[$group['Subdivision_ID']] = $preparatData;
        if (isset($group['childs'])) $result += preparationGroup($group['childs'], $group_field_white, $level + 1);
    }
    return $result;
}
