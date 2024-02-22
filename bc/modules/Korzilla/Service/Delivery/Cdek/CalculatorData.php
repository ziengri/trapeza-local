<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek;

class CalculatorData implements CalculatorDataInterface
{
    protected $deliveryCityCode;
    protected $products;
    protected $services;

    /**
     * Установить код горда отправки CDEK
     * 
     * @return static
     */
    public function setDeliveryCityCode($code)
    {
        $this->deliveryCityCode = $code;
        return $this;
    }

    /**
     * Установить продукты
     * 
     * @return static
     */
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    /**
     * Установить доп. услуги
     * 
     * @return static
     */
    public function setServices($services)
    {
        $this->services = $services;
        return $this;
    }

    public function getDeliveryCityCode()
    {
        return $this->deliveryCityCode;
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function getServices()
    {
        return $this->services;
    }
}
