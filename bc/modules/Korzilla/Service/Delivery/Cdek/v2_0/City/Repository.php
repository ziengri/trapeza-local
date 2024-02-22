<?php 

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\City;

use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;
use SimpleXMLElement;

class Repository
{
    const FILE_NAME = 'citylist.xml';

    /**
     * Сохранить список населенных пунтов
     * 
     * @param $cityList массив всех точек
     */
    public function save($cityList)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><cityList></cityList>');
        foreach ($this->clearList($cityList) as $city) {
            $this->arrayToXml($city, $xml->addChild('city'));
        }
        $xml->asXML($this->getFilePath());
    }

    /**
     * Получить список пунктов выдачи заказов
     * 
     * @return SimpleXMLElement|bool
     */
    public function get()
    {
        clearstatcache(true, $this->getFilePath());

        if (!file_exists($this->getFilePath())) return false;

        return new SimpleXMLElement($this->getFilePath(), 0, true);
    }

    /**
     * Получить дату последнего обновления
     * 
     * Возвращает дату в формате Unix Timestamp
     * 
     * @return int
     */
    public function lastUpdated()
    {
        clearstatcache(true, $this->getFilePath());
        return filectime($this->getFilePath()) ?: 0;
    }

    /**
     * Получить путь до файла харнения пунктов выдачи
     * 
     * @return string
     */
    private function getFilePath()
    {
        return ToolsAssist::getInstance()->getRepository()->getPublicDir().'/'.self::FILE_NAME;
    }

    /**
     * Занести данные из массива в дерево xml
     * 
     * @param array $array
     * @param SimpleXMLElement $xml
     */
    private function arrayToXml($array, $xml)
    {
        foreach ($array as $key => $value) {
            if (is_numeric($key)) $key = 'array_'.$key;
            if (is_array($value)) {
                $this->arrayToXml($value, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }
    }

    /**
     * Очистить новый список пунктов выдачи
     * 
     * @param array $list
     * 
     * @return array
     */
    private function clearList($list)
    {
        foreach ($list as &$item) {
            unset(
                $item['country_code'],
                $item['country'],
                $item['region'],
                $item['region_code'],
                $item['postal_codes'],
                $item['time_zone'],
                $item['payment_limit']
            );
        }
        return $list;
    }
}