<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek;

interface CalculatorDataInterface
{
    /**
     * Получить код города прибытия
     * 
     * @return string
     */
    public function getDeliveryCityCode();

    /**
     * Получить список продуктов
     * 
     * @return array
     */
    public function getProducts();

    /**
     * Получить список сервисов
     * 
     * @return array
     */
    public function getServices();
}