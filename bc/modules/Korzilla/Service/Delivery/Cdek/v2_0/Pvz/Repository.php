<?php 

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Pvz;

use App\modules\Korzilla\Service\Delivery\Cdek\ToolsAssist;
use SimpleXMLElement;

class Repository
{
    const FILE_NAME = 'v2_0_pvzlist.xml';

    /**
     * Сохранить список пунтов выдачи заказов
     * 
     * @param $pvzList массив всех точек
     */
    public function save($pvzList)
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><pvzList></pvzList>');
        foreach ($this->clearList($pvzList) as $pvz) {
            $this->arrayToXml($pvz, $xml->addChild('pvz'));
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
                $item['address_comment'],
                $item['nearest_station'],
                $item['phones'],
                $item['email'],
                $item['note'],
                $item['site'],
                $item['dimensions'],
                $item['work_time_exceptions'],
                $item['owner_code'],
                $item['take_only'],
                $item['is_dressing_room'],
                $item['have_cashless'],
                $item['have_cash'],
                $item['allowed_cod'],
                $item['office_image_list'],
                $item['work_time_list'],
                $item['work_time_list'],
                $item['weight_min'],
                $item['weight_max'],
                $item['location']['country_code'],
                $item['location']['region_code'],
                $item['location']['region'],
                $item['location']['fias_guid'],
                $item['location']['postal_code'],
                $item['fulfillment']
            );
        }
        return $list;
    }
}