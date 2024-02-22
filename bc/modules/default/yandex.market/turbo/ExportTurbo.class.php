<?php

namespace turbo;

use Class2001;

class ExportTurbo
{
    private $categoriesBlackList = [
        'Поиск по каталогу',
        'Сравнение товаров'
    ];


    public function __construct($token)
    {
        global $setting;
        $this->nc_core = \nc_Core::get_object();
        $this->db = $this->nc_core->db;
        $this->current_catalogue = $this->nc_core->catalogue->get_by_host_name(str_replace("www.", "", $_SERVER['HTTP_HOST']));
        $this->catalogue =  $this->current_catalogue['Catalogue_ID'];
        $this->setting = $setting;
        $this->domenUrl = ($_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'];
        $this->settingMearch = [
            'all_turbo' => 1,
            'pickup_turbo' => true,
            'store_turbo' => true,
            'delivery_turbo',
            'sales_notes_on_turbo',
            'sales_notes_turbo',
            'allnalich_turbo'
        ];
    }

    private function getCategories($categoriesXml)
    {

        $categories = $this->db->get_results("SELECT 
                                    a.Subdivision_ID,
                                    a.Subdivision_Name,
                                    a.DescriptionObj,
                                    a.Parent_Sub_ID,
                                    b.Class_ID
                                FROM 
                                    Subdivision AS a,
                                    Sub_Class AS b 
                                WHERE 
                                    " . ($this->settingMearch['all_turbo'] ? "" : "a.inMarket = 1 AND") . "
                                    a.Subdivision_ID = b.Subdivision_ID AND 
                                    b.Class_ID=2001 AND 
                                    a.Catalogue_ID = '{$this->catalogue}'", ARRAY_A);
                                
        if ($categories) {
            foreach ($categories as $categori) {
                if (in_array($categori['Subdivision_Name'], $this->categoriesBlackList)) {
                    continue;
                }

                $this->subs[$categori['Subdivision_ID']] = [
                    'name' => $categori['Subdivision_Name'],
                    'descr' => $categori['DescriptionObj'],
                ];

                $category = $categoriesXml->addChild('category', htmlspecialchars($categori['Subdivision_Name']));
                $category->addAttribute('id', $categori['Subdivision_ID']);
                if ($categori['Parent_Sub_ID']) {
                    $category->addAttribute('parentId', $categori['Parent_Sub_ID']);
                }
            }
        }
    }

    public function getItems()
    {
        $query_where = '';

        $balckListWordName = ['б/у', 'комиссионный', 'некомплект', 'потертости', 'некондиция'];

        foreach ($balckListWordName as $word) {
            $query_where .= ($query_where ? " AND " : null) . "`name` NOT LIKE '{$word}'";
        }

        $items = $this->db->get_results(
            "SELECT
                *
            FROM
                Message2001 
            WHERE
                Checked = 1 AND
                Price > 0 AND
                Subdivision_ID IN (" . implode(",", array_keys($this->subs)) . ") AND
                {$query_where}",
            ARRAY_A
        );
    
        foreach ($items as $item) {
            # Фотография
            $photo_data = $this->db->get_results(
                "SELECT 
                    Name,
                    Size,
                    Path,
                    Field_ID,
                    Preview, ID,
                    Priority 
                FROM 
                    Multifield 
                WHERE 
                    Field_ID = 2353 AND 
                    Message_ID = {$item['Message_ID']} 
                ORDER BY 
                    `Priority`",
                ARRAY_A
            );
            $item['photo'] = new \nc_multifield('photo', 'Фотографии', 0);
            if ($photo_data) $item['photo']->set_data($photo_data);
            $item['fullLink'] = nc_message_link($item['Message_ID'], 2001);
            $item['currency_id'] = $item['currency'];
            $item['orgName'] = $item['name'];
            $item['classID'] = 2001;

            $itemObj = new \Class2001($item);

            unset($item);

            $photos = $itemObj->getPhoto();

            if (empty($photos)) continue;

            $name = (is_numeric($itemObj->orgName) ? "{$this->subs[$itemObj->Subdivision_ID]} №{$itemObj->orgName}" : $itemObj->orgName);

            $description = "";
            $description = ($itemObj->text ?: "Приобрести {$name} Вы можете в компании {$this->current_catalogue['Catalogue_Name']} "
                                . ($itemObj->stock > 0 ? "в наличии {$itemObj->stock} шт" : "под заказ"));
        
            $itemsResult = [
                'available' => ($itemObj->stock > 0 || $this->settingMearch['allnalich_turbo'] ? "true" : "false"),
                'id' => $itemObj->Message_ID,
                'url' => $this->domenUrl . $itemObj->fullLink,
                'price' => floatval(str_replace(",", ".", $itemObj->price)),
                'categoryId' => $itemObj->Subdivision_ID,
                'store' => $this->settingMearch['store_turbo'],
                'pickup' => $this->settingMearch['pickup_turbo'],
                'delivery' => $this->settingMearch['pickup_turbo'],
                'name' => $name,
                'vendor' => htmlspecialchars($itemObj->vendor),
                'description' => htmlspecialchars(strip_tags($description)),
                'weight' => $itemObj->ves,
                'photos' => $photos
            ];

            if ($itemObj->priceBefore > $itemObj->price) {
                $itemsResult['oldprice'] = floatval(str_replace(",", ".", $itemObj->priceBefore));
            }

            if ($this->settingMearch['sales_notes_on_turbo']) {
                $itemsResult['sales_notes'] = $this->settingMearch['sales_notes_turbo'];
            }

            $otherParams = $itemObj->getParamsArray();
            if (count($otherParams)) {
                $itemsResult[$itemObj->Message_ID]['params'] = $otherParams;
            }

            $this->createItemYML($itemsResult);

            unset($itemsResult, $otherParams);
        }

        return $itemsResult;
    }

    private function createItemYML($item)
    {
        $offer = $this->ymlCatalog->shop->offers->addChild('offer');
        $offer->addAttribute('id', $item['id']);
        $offer->addAttribute('available', $item['available']);
            $offer->addChild('name', $item['name']);
            $offer->addChild('url', $item['url']);
            $offer->addChild('price', $item['price']);
            $offer->addChild('currencyId', 'RUR');
            $offer->addChild('categoryId', $item['categoryId']);
            $offer->addChild('store', $item['store']);
            $offer->addChild('pickup', $item['pickup']);
            $offer->addChild('delivery', $item['delivery']);
            $offer->addChild('description', $item['description']);
            $offer->addChild('weight', $item['weight']);
            $offer->addChild('sales_notes', $item['sales_notes']);
            foreach ($item['photos'] as $photo) {
                $offer->addChild('picture', (!strstr($photo, "://") ? $this->domenUrl : "") . $photo['path']);
            }
    }

    public function getFile()
    {
        return 1235;
    }

    public function setFile()
    {
        $this->ymlCatalog = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><!DOCTYPE yml_catalog SYSTEM "shops.dtd"><yml_catalog/>');
        $this->ymlCatalog->addAttribute('date', date("Y-m-d H:i"));
            $shop = $this->ymlCatalog->addChild('shop');
            $shop->addChild('name', $this->current_catalogue['Catalogue_Name']);
            $shop->addChild('company', $this->current_catalogue['Catalogue_Name']);
            $shop->addChild('url', $this->domenUrl);
                $currencies = $shop->addChild('currencies');
                    $currency = $currencies->addChild('currency');
                    $currency->addAttribute('id', 'RUR');
                    $currency->addAttribute('rate', 1);
                    $currency->addAttribute('plus', 0);
                $this->getCategories($shop->addChild('categories'));
                $shop->addChild('offers');
                $this->getItems();
        return $this->ymlCatalog->asXML();
    }

    /**
     * Проверка слова в строк
     *
     * @param string $string
     * @param string|array $patern
     * @return bool
     */
    private function WordInString($string, $patern)
    {
        if (is_array($patern)) {
            foreach ($patern as $word) {
                if (mb_stristr($string, $word)) {
                    return true;
                }
            }
        }

        if (mb_stristr($string, $word)) {
            return true;
        }

        return false;
    }
}
