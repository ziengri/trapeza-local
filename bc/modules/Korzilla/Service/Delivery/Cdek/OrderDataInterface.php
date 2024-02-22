<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek;

interface OrderDataInterface
{
    /**
     * Получить код тарифа
     * 
     * @return int
     */
    public function getTariffCode();

    /**
     * Получить код ПВЗ получения СДЭК
     * 
     * @return string
     */
    public function getDeliveryPoint();

    /**
     * Получить адрес прибытия
     * 
     * @return array
     */
    public function getToLocation();

    /**
     * Получить список продуктов
     * 
     * @return array
     */
    public function getProducts();

    /**
     * Получить данные о получателе
     * 
     * @return array
     */
    public function getRecipient();

    /**
     * Получить номер заказа
     * 
     * @return string|int|null
     */
    public function getOrderNumber();
    
    /**
     * Получить список сервисов
     * 
     * @return array
     */
    public function getServices();
}