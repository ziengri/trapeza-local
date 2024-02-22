<?php

namespace App\modules\Korzilla\YML;

use \Class2001;
use \Exception;
use \nc_Core;
use \XMLWriter;

class ImportYML
{

    protected const MAX_PHOTO = 200;
    protected const MAX_SIZE = 198;
    protected const MAX_OFFER_ELEMENTS = 30000;
    /**
     * @var nc_Core
     */
    protected $nc_core;
    /**
     * Счечик элементов в одном файле
     * 
     * @var int
     */
    protected $element_count = 0;
    /**
     * size_file
     *
     * @var int
     */
    protected $size_file = 0;
    /**
     * Текущий номер файла
     *
     * @var int
     */
    protected $chinck_xml = 0;
    /**
     * domen
     *
     * @var mixed
     */
    protected $domen;
    /**
     * catalogue
     *
     * @var int
     */
    protected $catalogue;
    /**
     * Разделы каталога
     *
     * @var array|null
     */
    protected $catigories;
    /**
     * Счетчик товаров для лога
     *
     * @var int
     */
    protected $itemCount = 0;
    /**
     * Путь до файла
     *
     * @var string
     */
    protected $path = '';
    /**
     * Путь до папки yml
     *
     * @var string
     */
    protected $rootDir = "";
    /**
     * xmlWriter
     *
     * @var XMLWriter
     */
    protected $xmlWriter;
    /**
     * message
     *
     * @var int
     */
    protected $message = 0;
    /**
     * setting
     *
     * @var array
     */
    protected $setting;
    /**
     * stopWord
     *
     * @var array
     */
    protected $stopWord = [
        'б/у',
        'комиссионный',
        'некомплект',
        'некондиция'
    ];
    /**
     * log
     *
     * @var array
     */
    public $log = [
        'status' => 1,
        'message' => 'Создается ...',
        'link' => [],
        'item' => 0,
        'total_item' => 0
    ];

    /** @var array настройки сайта */
    protected $siteSettings;

    /** @var array */
    protected $dopParams;


    protected $description = 'Приобрести %s Вы можете в компании %s %s';

    public function __construct($message)
    {
        $this->message = $message += 0;
        $this->setNcCore();
        $this->setSetting($message);
        $this->catalogue = $this->nc_core->catalogue->get_by_id($this->setting['Catalogue_ID']);
        $this->setRootDir();
        $this->catigories = $this->getCategories();
        $this->setTotalItemsLog($this->catigories);
        $this->domen = ($this->catalogue['https'] ? 'https://' : 'http://') . $this->catalogue['Domain'];

        $this->createYML();
    }

    /**
     * Создания yml файлов
     *
     * @return void
     */
    protected function createYML()
    {
        $this->path = $this->rootDir . 'yml' . $this->message . '.xml';
        $this->updateLog();

        $this->xmlWriter = new XMLWriter();
        $this->xmlWriter->openMemory();
        $this->xmlWriter->setIndent(true);

        $this->startBodyXML();

        foreach ($this->catigories as $category) {
            $this->setItems($category['Subdivision_ID']);
        }

        $this->endBodyXml();
        $link = "/yml/{$this->message}/";
        $this->log['link'][] = $link . '0/';
        for ($c = 1; $c <= $this->chinck_xml; $c++) {
            $this->log['link'][] = $link . "{$c}/";
        }
        $this->log['status'] = 2;
        $this->log['message'] = 'Обновлен ' . date("Y-m-d H:i:s.", filemtime($this->path));

        $this->updateLog();
    }

    /**
     * getSizeMb
     * 
     * Получения размера файла в мб
     *
     * @return int
     */
    protected function getSizeMb()
    {
        if (!file_exists($this->path))
            return 0;
        return number_format(filesize($this->path) / 1048576, 2);
    }

    /**
     * startBodyXML
     * 
     * Создания начальных элементов xml
     * @return void
     */
    protected function startBodyXML()
    {   
        //определение тайм зоны
        $currentTimezone = date_default_timezone_get();
        date_default_timezone_set($currentTimezone);

        $this->xmlWriter->startDocument('1.0', 'UTF-8');
        $this->xmlWriter->startDtd('yml_catalog', null, 'shops.dtd');
        $this->xmlWriter->endDtd();
        $this->xmlWriter->startElement("yml_catalog");
        // old $this->xmlWriter->writeAttribute("date", date("Y-m-d h:m"));
        $this->xmlWriter->writeAttribute("date", date("Y-m-d\TH:i:sP"));

        $this->xmlWriter->startElement("shop");
        $this->xmlWriter->writeElement("name", $this->catalogue['Catalogue_Name']);
        $this->xmlWriter->writeElement("company", $this->catalogue['Catalogue_Name']);
        $this->xmlWriter->writeElement("url", $this->domen);
        $this->xmlWriter->writeElement("platform", 'Korzilla');
        $this->xmlWriter->startElement("currencies");
        $this->xmlWriter->startElement("currency");
        $this->xmlWriter->writeAttribute("id", 'RUR');
        $this->xmlWriter->writeAttribute("rate", 1);

        $this->xmlWriter->endElement(); //currency
        $this->xmlWriter->endElement(); //currencies
        $this->xmlWriter->startElement("categories");

        foreach ($this->catigories as $category) {
            $this->xmlWriter->startElement("category");
            $this->xmlWriter->writeAttribute("id", $category['Subdivision_ID']);
            if ($category['Parent_Sub_ID'] > 0) {
                $this->xmlWriter->writeAttribute("parentId", $category['Parent_Sub_ID']);
            }
            $this->xmlWriter->text($category['Subdivision_Name']);
            $this->xmlWriter->endElement(); //category
        }
        $this->xmlWriter->endElement(); //categories
        if ($this->setting['delivery'] && $this->setting['cost'] > 0 && trim($this->setting['days']) != '') {
            $this->xmlWriter->startElement("delivery-options");
                $this->xmlWriter->startElement("option");
                    $this->xmlWriter->writeAttribute("cost",$this->setting['cost']);
                    $this->xmlWriter->writeAttribute("days",$this->setting['days']);
                $this->xmlWriter->endElement(); //options
            $this->xmlWriter->endElement(); //delivery-options
        }

        $this->xmlWriter->startElement("offers");
        $this->saveFile($this->path, 1);
    }

    /**
     * endBodyXml
     * 
     * Создания завершающих элементов xml
     * @return void
     */
    protected function endBodyXml()
    {
        $this->xmlWriter->endElement(); //offers
        $this->xmlWriter->endElement(); //shop
        $this->xmlWriter->endElement(); //yml_catalog
        $this->xmlWriter->endDocument();
        $this->saveFile($this->path);
    }

    /**
     * saveFile
     *
     * Сохранение файла YML
     * 
     * @param  string $pathFile
     * @param  bool $new
     * @return void
     */
    protected function saveFile($pathFile, $new = false)
    {
        if (!file_put_contents($pathFile, $this->xmlWriter->flush(true), ($new ? 0 : FILE_APPEND))) {
            $this->log['status'] = 0;
            $this->log['message'] = 'Ошибка записи файла';
            $this->updateLog();
            die;
        }
    }

    /**
     * getItems
     *
     * Получение товаров сайта
     * 
     * @param  int $categoryID
     * @return array
     */
    protected function getItems($categoryID)
    {
        return $this->nc_core->db->get_results(
            "SELECT
                `Message_ID`,
                `name`,
                {$this->setting['price']} as price_export,
                {$this->setting['stock']} as stock_export
            FROM
                Message2001
            WHERE
                Checked = 1 AND
                dogovor != 1 AND
                {$this->setting['price']} > 0 AND 
                " . (!$this->setting['all_items_in_stock'] ? "{$this->setting['stock']} > 0 AND" : '') . " 
                Subdivision_ID = {$categoryID}",
            ARRAY_A
        ) ?: [];
    }

    /**
     * setTotalItemsLog
     *
     * Устоновка общего количества товаров
     * 
     * @param  array $catigories
     * @return void
     */
    protected function setTotalItemsLog($catigories)
    {

        $catigories = implode(',', array_column($catigories, 'Subdivision_ID'));

        $this->log['total_item'] = $this->nc_core->db->get_var(
            "SELECT
                COUNT(*)
            FROM
                Message2001
            WHERE
                Checked = 1 AND
                {$this->setting['price']} > 0 AND 
                " . (!$this->setting['all_items_in_stock'] ? "{$this->setting['stock']} > 0 AND" : '') . " 
                Subdivision_ID IN ({$catigories})"
        ) ?: 0;
        $this->updateLog();
    }

    /**
     * getCategories
     *
     * Получения разделов сайта
     * 
     * @return array|null
     */
    protected function getCategories()
    {

        return $this->nc_core->db->get_results(
            "SELECT
                a.Subdivision_ID,
                a.Subdivision_Name,
                a.DescriptionObj,
                a.Parent_Sub_ID,
                b.Class_ID
            FROM
                Subdivision as a,
                Sub_Class as b
            WHERE
                " . (empty($this->setting['all_item']) ? "a.inMarket = 1 AND" : '') . "
                a.Subdivision_ID = b.Subdivision_ID AND
                b.Class_ID=2001 AND
                a.Catalogue_ID = {$this->catalogue['Catalogue_ID']}
            ",
            ARRAY_A
        );
    }

    /**
     * setItems
     *
     * Создания массива товров по id категории
     * 
     * @param  int $categoryID
     * @return void
     */
    protected function setItems($categoryID)
    {
        foreach ($this->getItems($categoryID) as $item) {

            $name = trim(htmlspecialchars(strip_tags($item['name'])));

            if ($this->checkWord($name, $this->stopWord))
                continue;
            try {
                $itemObj = Class2001::getItemById($item['Message_ID']);
            } catch (Exception $e) {
                echo $e->getMessage();
                continue;
            }
            $photos = $itemObj->getPhoto();

            if ($this->setting['turbo'] && empty($photos))
                continue;

            if ($this->setting['one_photo'])
                $photos = [$photos[0]];

            $description = $this->setting['def_description'] ?: (strip_tags($itemObj->text) ? $itemObj->text : sprintf($this->description, $name, $this->catalogue['Catalogue_Name'], ($item['stock_export'] > 0 ? "в наличии {$item['stock_export']} шт" : "под заказ")));

            $description = trim(htmlspecialchars(strip_tags(preg_replace('/(?:|)/m', '', $description))));

            // $itemObj->price = $itemObj->{$this->setting['price']};
            /** Поправил логику обработки цены */
            $itemObj->price = (float) $item['price_export'];
            $itemObj->setPrice();
            
            $params = [
                'id' => $item['Message_ID'],
                'available' => ($this->setting['all_items_in_stock'] || $item['stock_export'] > 0 ? 'true' : 'false'),
                'url' => $this->domen . $itemObj->fullLink,
                'price' => floatval(str_replace(",", ".", $itemObj->price)),
                'categoryId' => $categoryID,
                'picture' => $photos ?: [],

                'delivery' => $this->setting['delivery'] ? 'true' : 'false',
                'name' => $name,
                'params' => $itemObj->getParamsArray(),
                'vendor' => $itemObj->vendor,
                'description' => $description,
                'sales_notes' => ($this->setting['sales_notes_on'] && $this->setting['sales_notes'] ? $this->setting['sales_notes'] : null)
            ];

            $this->setOffer($params);

            if (!empty($itemObj->variable)) {
                $variable = orderArray($itemObj->variable);
                foreach ($variable as $variantid => $variant) {
                    $nameV = trim(htmlspecialchars(strip_tags($variant['name'])));
                    if (empty($name) || $this->checkWord($name, $this->stopWord) && $variant['price'] <= 0)
                        continue;
                    $itemObj->price = $variant['price'];
                    $itemObj->setPrice();

                    $params['id'] = $item['Message_ID'] . 'v' . $variantid;
                    $params['name'] = $name . ' ' . $nameV;
                    $params['price'] = price($itemObj->price);

                    $this->setOffer($params);
                }
            }
            $this->element_count++;
            if ($this->element_count >= self::MAX_OFFER_ELEMENTS || self::MAX_SIZE <= $this->getSizeMb()) {
                $this->endBodyXml();
                $this->element_count = 0;
                $this->chinck_xml++;
                $this->path = $this->rootDir . 'yml' . $this->message . '_' . $this->chinck_xml . '.xml';
                $this->startBodyXML();
            }
        }
    }

    /**
     * setOffer
     * 
     * Создание offer товара
     * @param  array $param
     * @return void
     */
    protected function setOffer($param)
    {
        $this->xmlWriter->startElement("offer");
        $this->xmlWriter->writeAttribute("id", $param['id']);
        $this->xmlWriter->writeAttribute("available", $param['available']);
        $this->xmlWriter->writeElement("url", $param['url']);
        $this->xmlWriter->writeElement("price", $param['price']);
        $this->xmlWriter->writeElement("currencyId", 'RUR');
        $this->xmlWriter->writeElement("categoryId", $param['categoryId']);
        foreach ($param['picture'] as $picture) {
            if (!stristr($picture['path'], "://"))
                $picture['path'] = $this->domen . $picture['path'];
            $this->xmlWriter->writeElement("picture", $picture['path']);
        }
        foreach ($param['params'] as $itemParameter) {
            if ($itemParameter['name'] === "Бренд") {
                // Если имя параметра - 'Вес, кг', создаем тег 'weight'
                $this->xmlWriter->startElement('vendor');
                $this->xmlWriter->text($itemParameter['value']);
                $this->xmlWriter->endElement();
            } else {
                $this->xmlWriter->startElement('param');
                $this->xmlWriter->writeAttribute('name', $itemParameter['name']);
                $this->xmlWriter->text($itemParameter['value']);
                $this->xmlWriter->endElement();
            }

        }
        $this->xmlWriter->writeElement("name", htmlspecialchars_decode($param['name']));
        // $this->xmlWriter->writeElement("pickup", $param['pickup']);
        $this->xmlWriter->writeElement("delivery", $param['delivery']);
        //проверка на пустой вендор 
        if (isset($param['vendor']) && !empty($param['vendor'])) {
            $this->xmlWriter->writeElement("vendor", $param['vendor']);
        }
        $this->xmlWriter->startElement("description");
        $this->xmlWriter->writeCData($param['description']);
        $this->xmlWriter->endElement();
        if ($param['sales_notes'] != NULL) {
            $this->xmlWriter->writeElement("sales_notes", $param['sales_notes']);

        }
        $this->xmlWriter->endElement();
        $this->saveFile($this->path);

        $this->log['item'] = ++$this->itemCount;
        $this->updateLog();
    }
    /**
     * Установка значений настроек объекта
     * 
     * @param int $message id объекта
     * 
     * @return void
     */
    protected function setSetting($message)
    {
        $message += 0;
        $this->setting = $this->nc_core->db->get_row("SELECT * FROM Message2258 WHERE Message_ID = {$message} LIMIT 1", ARRAY_A);

        if (empty($this->setting))
            throw new Exception('Настройки не найдены');
    }
    /**
     * Подключения ядра
     * 
     * @return void
     */
    protected function setNcCore()
    {
        $this->nc_core = nc_Core::get_object();
    }


    /**
     * setRootDir
     *
     * Создане корневой деректории yml
     * @return void
     */
    protected function setRootDir()
    {
        $this->rootDir = $_SERVER['DOCUMENT_ROOT'] . '/a/' . $this->catalogue['login'] . '/yml/import/';

        @mkdir($this->rootDir, 0777, true);
        if (!is_dir($this->rootDir))
            throw new Exception('Ошибка при создании деректории');
    }

    /**
     * updateLog
     *
     * Запись лога
     * @return void
     */
    protected function updateLog()
    {
        if (!file_put_contents($this->rootDir . 'yml' . $this->message . '.log', json_encode($this->log, JSON_UNESCAPED_UNICODE))) {
            throw new Exception('Ошибка записи лога!');
        }
    }

    /**
     * checkWord
     *
     * Проверка строки на подстроку
     * 
     * @param  string $haystack
     * @param  array|string $needles
     * @return bool
     */
    protected function checkWord($haystack, $needles)
    {
        if (is_array($needles)) {
            foreach ($needles as $str) {
                $pos = mb_strpos($haystack, $str);
                if ($pos !== FALSE)
                    return true;
            }
        } else {
            return (strpos($haystack, $needles) !== FALSE ? true : false);
        }
        return false;
    }
}
