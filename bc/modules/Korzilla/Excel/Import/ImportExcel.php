<?php

namespace App\modules\Korzilla\Excel\Import;

setlocale(LC_ALL, 'ru_RU.UTF-8');

use nc_Core;
use ZipArchive;
use Class2001;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class ImportExcel
{
    protected const GROUP_STATIC_FIELD = [
        'Subdivision_ID' => 'Ид раздела',
        'Subdivision_Name' => 'Наименовния раздела'
    ];

    protected const GROUP_FIELD = [
        'AlterTitle' => 'Альтернативное наименования внутри раздела',
        'text' => 'Описание в разделе (сверху)',
        'text2' => 'Описание в разделе (снизу)',
        // 'descr' => 'Краткое описание',
        'find' => 'Выбор товаров по фразе',
        'sub_find' => 'В каких разделах искать',
        'strictFind' => 'Строгий поиск по фразе',
    ];

    protected const GRUOP_HARD_FIELD = [
        'tags_links' => 'Тэги сверху',
        'tags_links_bottom' => 'Тэги снизу',
        'defaultOrder' => 'Сортировка по умолчанию',
        // 'citytarget' => 'Города таргетинга',
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
        'view_obj_by_param' => 'Показывать объекты по выбранным параметрам',
    ];


    protected const GROUP_FIELD_SEO = [
        'no_index_seo' => 'Не индексировать раздел',
        'seoTextBottom2' => 'SEO текст снизу страницы для под. разделов',
        'seoTextBottom' => 'Сео текст под контентом',
        'TitleObj' => 'Заголовок карточки объекта (Title)',
        'TitleImg' => 'Альтернативный текст для изображений (Alt)',
        'Title2' => 'Заголовок раздела (Title)',
        'KeywordsObj' => 'Ключевые слова карточки объекта',
        'Keywords2' => 'Шаблон ключевых слов страницы (Keywords)',
        'DescriptionObj' => 'Описание карточки объекта',
        'inMarket' => 'Выгружать в Яндекс.Маркет',
        'Description2' => 'Шаблон описания страницы (Description)',
        'AlterTitleObj' => 'Заголовок H1 карточки объекта',
        'seotext' => 'Шаблон seo-текста в карточке товара',
        'AlterTitle2' => 'Шаблон заголовка h1',
        'rss_turbo_yandex' => 'RSS для контентных турбо станиц',
    ];

    protected const GROUP_FIELD_SETTING = [
        'txttoall' => 'Использовать текст для всех товаров раздела',
        'sub_lang' => 'Язык вывода',
        // 'allGoods' => 'Вывести все товары с сайта',
        // 'instock' => 'Товары только в наличии',
        'imgtoall' => 'Использовать изображение для всех товаров раздела',
        // 'ajaxload' => 'Плавная подгрузка данных',
        'noleftcol' => 'Скрывать левую колонку (зона 1)',
        'subdir' => 'Показ подразделов этого раздела',
        'outallitem' => 'Вывод товаров из подразделов',
        'filtermenu' => 'Подразделы как фильтр',
        'blank' => 'Открывать в новом окне',
    ];

    protected const ITEM_STATIC_FIELD = [
        'Message_ID' => 'Ид товара на сайте',
        '' => null,
        'name' => 'Название'
    ];

    protected const ITEM_FIELD = [
        'h1' => 'Альтернативное название H1',
        'art' => 'Артикул',
        'art2' => 'Артикул 2 или код',
        'vendor' => 'Производитель',

        'price' => 'Цена',
        'price2' => 'Цена 2',
        'price3' => 'Цена 3',
        'price4' => 'Цена 4',

        'discont' => 'Скидка на товар (в %)',
        'pricediscont' => 'или цена со скидкой',
        'disconttime' => 'Скидка действует до',
        'timer' => 'Таймер обратного отсчета',

        'firstprice' => 'Это нижняя граница цены товара (от)',
        'dogovor' => 'Цена договорная (число не показывается)',
        'torg' => 'Возможен торг',
        'notmarkup' => 'Не учитывать общую наценку',

        'currency' => 'Валюта в прайсе',

        'stock' => 'Наличие на складе (шт.)',
        'stock2' => 'Наличие на складе №2 (шт.)',
        'stock3' => 'Наличие на складе №3 (шт.)',
        'stock4' => 'Наличие на складе №4 (шт.)',

        'text' => 'Полное описание',
        'text2' => 'Характеристики',
        'descr' => 'Краткое описание в списке товаров',

        'photourl' => 'URL фото через запятую',
        'all_photo_item' => 'Фото товара. (Не учавствует в загрузке на сайт)'
    ];

    protected const ITEM_FIELD_SEO = [
        'ncTitle' => 'Заголовок страницы (Title)',
        'ncDescription' => 'Описание страницы (Description)',
        'ncKeywords' => 'Ключевые слова страницы (Keywords)',
    ];

    protected const ITEM_FIELD_SETTING = [
        'spec' => 'Спецпредложение',
        'new' => 'Новинки',
        'action' => 'Хит продаж',

        'itemlabel' => 'Лейбл на товаре',

        'ves' => 'Вес',
        'edizm' => 'Единица измерения',
        'capacity' => 'Объем',
        'sizes_item' => 'Размер',
        'height' => 'Высота',
        'width' => 'Ширина',
        'length' => 'Длина',
        'depth' => 'Глубина',
        'params' => 'Доп. Параметры товара',
        'Checked' => 'Вкл/выкл товар',

        'variablename' => 'Название варианта',

        'buywith' => 'C этим товаром покупают (артикулы по одному в строке)',
        'analog' => 'Список артикулов аналогичных товаров (по одному в строке)',

        'Priority' => 'Приоритет товара в разделе',

        'nocart' => 'Запретить добавлять товар в корзину',
        'noorder' => 'Запретить заказывать товар',

        'tags' => 'Тэги',
        'extlink' => 'Внешняя ссылка',
        'lang' => 'Язык вывода'
    ];

    protected const ITEM_HARD_FIELD = [
        'Subdivision_IDS' => 'Доп. разделы в которых показывать товар (номера через запятую)',

        'citytarget' => 'Товар доступен только в городах (по-умолчанию - везде)',
        'pricecity' => 'Цены по городам',

        'colors' => 'Цвет товара',
        'buycolors' => 'Обязательный выбор цвета товара',

        'outItems' => 'Вывод объектов (портфолио, фотогалерея)',
        'code' => 'Код',
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
    ];

    protected $process;

    protected $total_count = 0;

    protected $current_count = 0;

    public $setting;

    public $settingImport;

    public function __construct(int $Catalogue_ID)
    {
        $this->setNcCore();
        $this->catalogue = $this->getCatalogue($Catalogue_ID);
        $this->setting = getSettings();
        $this->ROOTDIR = $_SERVER['DOCUMENT_ROOT'];
        $this->path = $this->ROOTDIR . '/a/' . $this->catalogue['login'] . '/reverse_unloading/import_excel/';
        $this->salt = md5(md5($this->catalogue['login']) . 2004);
        $this->itemFieldsWite = array_merge(self::ITEM_FIELD, self::ITEM_FIELD_SEO, self::ITEM_FIELD_SETTING, self::ITEM_HARD_FIELD);
        $this->groupFieldsWite = array_merge(self::GROUP_FIELD, self::GROUP_FIELD_SEO, self::GROUP_FIELD_SETTING, self::GRUOP_HARD_FIELD);
        @mkdir($this->path, 0777, true);
        $this->settingImport = $this->getSettings();
    }

    public function getFields()
    {
        $result = ['gruop' => [], 'item' => []];

        foreach (self::GROUP_FIELD as $field => $name) {
            $result['gruop']['basic'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['gruop'][$field]) ? $this->settingImport['gruop'][$field] : 0)];
        }

        foreach (self::GROUP_FIELD_SEO as $field => $name) {
            $result['gruop']['seo'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['gruop'][$field]) ? $this->settingImport['gruop'][$field] : 0)];
        }

        foreach (self::GROUP_FIELD_SETTING as $field => $name) {
            $result['gruop']['more'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['gruop'][$field]) ? $this->settingImport['gruop'][$field] : 0)];
        }

        foreach (self::GRUOP_HARD_FIELD as $field => $name) {
            $result['gruop']['hard'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['gruop'][$field]) ? $this->settingImport['gruop'][$field] : 0)];
        }


        foreach (self::ITEM_FIELD as $field => $name) {
            $result['item']['basic'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['item'][$field]) ? $this->settingImport['item'][$field] : 0)];
        }

        foreach (self::ITEM_FIELD_SEO as $field => $name) {
            $result['item']['seo'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['item'][$field]) ? $this->settingImport['item'][$field] : 0)];
        }

        foreach (self::ITEM_FIELD_SETTING as $field => $name) {
            $result['item']['more'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['item'][$field]) ? $this->settingImport['item'][$field] : 0)];
        }

        foreach (self::ITEM_HARD_FIELD as $field => $name) {
            $result['item']['hard'][$field] = ['name' => $name, 'value' => (isset($this->settingImport['item'][$field]) ? $this->settingImport['item'][$field] : 0)];
        }

        return $result;
    }
    public function setFields($fields)
    {
        foreach ($this->groupFieldsWite as $field => $value) {
            if (!isset($fields['gruop'][$field])) $fields['gruop'][$field] = 0;
        }

        foreach ($this->itemFieldsWite as $field => $value) {
            if (!isset($fields['item'][$field])) $fields['item'][$field] = 0;
        }

        $this->setSetting($fields);
    }

    public function setSetting($data)
    {
        foreach ($data as $key => $value) {
            $this->settingImport[$key] = $value;
        }

        file_put_contents($this->path . 'setting.json', json_encode($this->settingImport));
    }

    public function getSettings()
    {
        return json_decode((@file_get_contents($this->path . 'setting.json') ?: '{}'), true);
    }

    public function getCatalog()
    {
        $pathFile = $this->path . 'catalog_' .  $this->salt . '.csv';

        if ($this->settingImport['item']['params']) {
            $params_list = array_reduce($this->setting['lists_params'], function ($carry, $param) {
                if (!empty($param['name'])) {
                    $key = 'param_' . $param['keyword'];
                    $carry[$key] = $param['name'];
                }
                return $carry;
            }, []);
        }

        $itemFields = self::ITEM_STATIC_FIELD;

        foreach ($this->itemFieldsWite as $field => $name) {
            if (isset($this->settingImport['item'][$field]) && $this->settingImport['item'][$field]) {
                switch ($field) {
                    case 'params':
                        $itemFields = array_merge($itemFields, $params_list);
                        break;
                    default:
                        $itemFields[$field] = $name;
                        break;
                }
            }
        }

        $groupFields = self::GROUP_STATIC_FIELD;
        foreach ($this->groupFieldsWite as $field => $name) {
            if (isset($this->settingImport['gruop'][$field]) && $this->settingImport['gruop'][$field]) {
                switch ($field) {
                    default:
                        $groupFields[$field] = $name;
                        break;
                }
            }
        }


        $groups = $this->getGroups($groupFields);

        $gruop_key = array_keys($groups);
        $this->total_count = $this->nc_core->db->get_var("SELECT COUNT(*) FROM Message2001 WHERE Subdivision_ID IN (" . implode(',', $gruop_key) . ") AND name != ''") + count($gruop_key);

        $this->procesLog(['message' => 'Создаем'], true);

        $fp = fopen($pathFile, 'w');
        // fputs($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
        $header = $this->getHeader($itemFields, $groupFields);

    
        foreach ($header as $headerRow) {
            $this->fputcsv_eol($fp, $headerRow, "\r\n");
        }

        if ($this->settingImport['get_photo_zip']) {
            $this->pathGruopPhote = $this->path . 'photo/gruop/';
            @mkdir($this->pathGruopPhote, 0777, true);
            $this->pathItemPhote = $this->path . 'photo/item/';
            @mkdir($this->pathItemPhote, 0777, true);
            $this->total_count = $this->total_count * 2;
        }

        foreach ($groups as $group_id => $group) {
            $groupItems = $this->nc_core->db->get_results(
                "SELECT
                    *
                FROM 
                    Message2001 
                WHERE 
                    Subdivision_ID = {$group_id}
                    AND name != ''
                    AND Catalogue_ID = '{$this->catalogue['Catalogue_ID']}'
                ORDER BY Priority",
                ARRAY_A
            ) ?: [];

            if ($this->settingImport['get_photo_zip']) {
                $this->setPhote($group_id, $this->pathGruopPhote, 'gruop');
            }

            $groupRow = array_reduce($header['subField'], function ($carry, $groupField) use ($group) {
                $carry[] = @$group[$groupField];
                return $carry;
            }, []);
            $this->fputcsv_eol($fp, $groupRow, "\r\n");
            $this->current_count++;

            foreach ($groupItems as $index => $item) {

                if ($this->settingImport['get_photo_zip']) {
                    $this->setPhote($item['Message_ID'], $this->pathItemPhote, 'item');
                }

                if (!empty($item['params'])) {
                    $params = array_reduce(array_filter(explode("\r\n", $item['params'])), function ($carry, $param_row) {
                        $param_cols = explode('||', $param_row);
                        $carry[$param_cols[0]] = trim($param_cols[1], '|');
                        return $carry;
                    }, []);
                    $item = array_merge($item, $params);
                    unset($item['params']);
                }

                if (in_array('all_photo_item', $header['itemField'])) {
                    $item['all_photo_item'] = implode(',', array_column($this->getPhotos($item['Message_ID'], 'item'), 'path'));
                }

                $itemRow = array_reduce($header['itemField'], function ($carry, $itemField) use ($item) {
                    $itemField = preg_replace('/^param_/', '', $itemField);
                    @$carry[] = $item[$itemField];
                    return $carry;
                }, []);

                $this->fputcsv_eol($fp, $itemRow, "\r\n");
                $this->current_count++;

                unset($groupItems[$index], $params,  $itemRow);
            }

            $this->procesLog(['procent' => $this->getProcent()]);
        }

        fclose($fp);
        if ($this->settingImport['get_photo_zip']) {
            $this->procesLog(['message' => 'Архивируем ...']);
            $this->zip();
            $this->recursiveRemoveDir($this->path . 'photo/');
        }
        $this->procesLog([
            'status' => 1,
            'link' => str_replace($this->ROOTDIR, '', $pathFile),
            'link_date' => (file_exists($pathFile) ? date("Y m d H:i:s.", filemtime($pathFile)) : ''),
            'link_photo' => (file_exists($this->photoPath) ? str_replace($this->ROOTDIR, '', $this->photoPath) : ''),
            'link_photo_date' => (file_exists($this->photoPath) ? date("Y m d H:i:s.", filemtime($this->photoPath)) : ''),
        ]);
    }

    protected function getHeader($itemFields, $groupFields)
    {
        $total_count_cols = count($groupFields) > count($itemFields) ? count($groupFields) : count($itemFields);

        $itemsKey = array_keys($itemFields);
        $groupKey = array_keys($groupFields);

        $header = ['subFieldName' => [], 'subField' => [], 'itemFieldName' => [], 'itemField' => []];

        for ($i = 0; $i <= $total_count_cols; $i++) {
            $header['subFieldName'][] = @$groupFields[$groupKey[$i]];
            $header['subField'][] = @$groupKey[$i];
            $header['itemFieldName'][] = @$itemFields[$itemsKey[$i]];
            $header['itemField'][] = @$itemsKey[$i];
        }

        return $header;
    }

    protected function getPhotos($id, $type)
    {
        switch ($type) {
            case 'gruop':
                return nc_file_path('Subdivision', $id, 'img');
                break;
            case 'item':
                $item = @\Class2001::getItemById($id);
                return $item->photos;
                break;
        }
    }

    protected function setPhote($id, $path, $type)
    {
        switch ($type) {
            case 'gruop':
                if (($photoPath = nc_file_path('Subdivision', $id, 'img')) !== false) {
                    $photoPath = $this->ROOTDIR . $photoPath;
                    $pathTo = $path . $id . '/' . pathinfo($photoPath, PATHINFO_BASENAME);
                    $this->copyPhoto($photoPath, $pathTo);
                }
                break;
            case 'item':
                $item = @Class2001::getItemById($id);
                foreach ($item->photos as $photo) {
                    $photoPath = $this->ROOTDIR . $photo['path'];

                    if (file_exists($photoPath)) {
                        $pathTo = $path . $id . '/' . pathinfo($photoPath, PATHINFO_BASENAME);
                        $this->copyPhoto($photoPath, $pathTo);
                    }
                }
                break;
        }

        $this->current_count++;
    }

    protected function copyPhoto($pathFrom, $pathTo)
    {
        if (file_exists($pathFrom) && !file_exists($pathTo)) {
            @mkdir(dirname($pathTo), 0777, true);
            copy($pathFrom, $pathTo);
        }
    }
    protected function zip()
    {
        $zip = new ZipArchive();
        $rootPath = $this->path . 'photo/';
        $this->photoPath = $this->path . 'photo_' . $this->salt . '.zip';
        $zip->open($this->photoPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Создание рекурсивного итератора каталогов

        /** @var SplFileInfo[] $files */

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), RecursiveIteratorIterator::LEAVES_ONLY);
        foreach ($files as $name => $file) {
            // Пропустите каталоги (они будут добавлены автоматически)
            if (!$file->isDir()) {
                // Получение реального и относительного пути для текущего файла
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath));
                // Добавить текущий файл в архив
                $zip->addFile($filePath, $relativePath);
            }
        }
        // Zip-архив будет создан только после закрытия объекта
        $zip->close();
    }

    protected function recursiveRemoveDir($dir)
    {

        $includes = glob($dir . '/*');

        foreach ($includes as $include) {

            if (is_dir($include)) {

                $this->recursiveRemoveDir($include);
            } else {

                unlink($include);
            }
        }

        rmdir($dir);
    }

    protected function getGroups($fields)
    {
        $groups = $this->nc_core->db->get_results(
            "SELECT 
                sub.*
            FROM 
                Subdivision as sub,
                Sub_Class as cc
            WHERE 
                sub.Catalogue_ID = '{$this->catalogue['Catalogue_ID']}'
                AND sub.systemsub != 1
                AND sub.nosettings != 1
                AND sub.EnglishName NOT IN ('comparison', 'spec', 'new', 'hits')
                AND sub.Subdivision_ID = cc.Subdivision_ID
                AND cc.Class_ID = 2001
            ORDER BY sub.Hidden_URL DESC",
            ARRAY_A
        );

        $groups = array_reduce($groups, function ($carry, $group) {
            $carry[$group['Subdivision_ID']] = $group;
            return $carry;
        }, []);

        foreach ($groups as $id => $group) {
            if (isset($groups[$group['Parent_Sub_ID']])) {
                $groups[$group['Parent_Sub_ID']]['childs'][$id] = $groups[$id];
                unset($groups[$id]);
            }
        }

        return $this->preparationGroup($groups, $fields);
    }

    protected function preparationGroup($groups, $group_field_white, $level = 0)
    {
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

            if (!empty($preparatData))  $result[$group['Subdivision_ID']] = $preparatData;
            if (isset($group['childs'])) $result += $this->preparationGroup($group['childs'], $group_field_white, $level + 1);
        }
        return $result;
    }
    protected function getProcent()
    {
        return round(($this->current_count * 100 / $this->total_count), 2);
    }

    public function procesLog($data = [], $newProcess = false)
    {
        $path = $this->path . 'process.json';
        if (empty($this->process)) $this->process = $this->getProcess($newProcess);
        foreach ($data as $key => $value) {
            $this->process[$key] = $value;
        }

        file_put_contents($path, json_encode($this->process));
    }

    public function getProcess($newProcess = false)
    {
        $path = $this->path . 'process.json';

        $patern = [
            'status' => 0,
            'link' => '',
            'link_photo' => '',
            'message' => '',
            'procent' => 0
        ];

        return ($newProcess || !file_exists($path) ? $patern : json_decode(file_get_contents($path), 1));
    }

    public function getData()
    {
        return [self::GROUP_FIELD, self::GROUP_FIELD_SEO, self::GROUP_FIELD_SETTING, self::ITEM_FIELD];
    }

    protected function fputcsv_eol($fp, $array, $eol)
    {

        foreach ($array as $key => $value) {
            $array[$key] = mb_convert_encoding($value, 'windows-1251', 'UTF-8');
        }
        fputcsv($fp, $array, ';');
        if ("\n" != $eol && 0 === fseek($fp, -1, SEEK_CUR)) {
            fwrite($fp, $eol);
        }
    }
    protected function getCatalogue(int $Catalogue_ID)
    {
        return $this->nc_core->catalogue->get_by_id($Catalogue_ID);
    }

    protected function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }
}
