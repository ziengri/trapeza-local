<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\Pvz;

use SimpleXMLElement;

class Pvz
{
    /**
     * @param SimpleXmlElement
     */
    private $pvz;

    /**
     * @param SimpleXmlElement $pvz данные пункта выдачи заказов
     */
    public function __construct($pvz)
    {
        $this->pvz = $pvz;   
    }

    /**
     * Получить код
     * 
     * @return string
     */
    public function getCode()
    {
        return (string) $this->pvz->code;
    }

    /**
     * Получить название
     * 
     * @return string 
     */
    public function getName()
    {
        return (string) $this->pvz->name;
    }

    /**
     * Получить адрес
     * 
     * Адрес (улица, дом, офис) в указанном городе
     * 
     * @return string
     */
    public function getAddress()
    {
        return (string) $this->pvz->location->address;
    }

    /**
     * Получить полный адрес
     * 
     * Полный адрес с указанием страны, региона, города, и т.д.
     * 
     * @return string
     */
    public function getAddressFull()
    {
        return (string) $this->pvz->location->address_full;
    }

    /**
     * Получить координаты
     * 
     * массив ['долгота','широта']
     * 
     * @return array
     */
    public function getCoordinates()
    {
        return [
            str_replace(',', '.', $this->pvz->location->longitude),
            str_replace(',', '.', $this->pvz->location->latitude),
        ];
    }

    /**
     * Получить время работы
     * 
     * @return string
     */
    public function getWorkTime()
    {
        return (string) $this->pvz->work_time;
    }

    /**
     * Получить тип
     * 
     * @return string
     */
    public function getType()
    {
        return (string) $this->pvz->type;
    }

    /**
     * Получить название города
     * 
     * @return string
     */
    public function getCityName()
    {
        return (string) $this->pvz->location->city;
    }

    /**
     * Получить код города
     * 
     * @return int
     */
    public function getCityCode()
    {
        return (string) $this->pvz->location->city_code;
    }

    /**
     * Является пунктом выдачи заказов
     * 
     * @return bool
     */
    public function isHandout()
    {
        return !! (string) $this->pvz->is_handout;
    }

    /**
     * Является пунктом приема заказов
     * 
     * @return bool
     */
    public function isReception()
    {
        return !! (string) $this->pvz->is_reception;
    }
}