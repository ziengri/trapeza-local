<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek\v2_0\City;

use SimpleXMLElement;

class City
{
    /**
     * @var SimpleXMLElement
     */
    private $city;

    /**
     * @param SimpleXMLElement $city
     */
    public function __construct($city)
    {
        $this->city = $city;
    }

    /**
     * Получить код населенного пункта СДЭК
     * 
     * @return string
     */
    public function getCode()
    {
        return (string) $this->city->code;
    }

    /**
     * Получить название населенного пункта
     * 
     * @return string
     */
    public function getName()
    {
        return (string) $this->city->city;
    }

    /**
     * Получить координаты центра населенного пункта
     * 
     * массив ['долгота','широта']
     * 
     * @return array
     */
    public function getCoordinates()
    {
        return [
            (string) $this->city->longitude,
            (string) $this->city->latitude,
        ];
    }
}