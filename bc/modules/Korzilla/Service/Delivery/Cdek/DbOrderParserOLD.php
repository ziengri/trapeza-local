<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek;

use Class2001;

class DbOrderParser implements OrderDataInterface
{
    protected $order;
    protected $orderList;
    protected $customForm;

    /**
     * @param array $order массив заказа из базы
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function getTariffCode()
    {
        return $this->getOrderList()['delivery']['assist']['tariffId'];
    }

    public function getDeliveryPoint()
    {
        return $this->getOrderList()['delivery']['assist']['pvzCode'];
    }

    public function getToLocation()
    {
        return [
            'code' => $this->getOrderList()['delivery']['assist']['cityCode'] ?? null,
            'address' => $this->getCustomForm()['address']['value'] ?? null,
        ];
    }

    public function getRecipient()
    {
        return [
            'name' => $this->order['fio'], 
            'phones' => [
                'number' => $this->order['phone'],
            ]
        ];
    }

    public function getProducts()
    {
        $products = [];
        foreach ($this->getOrderList()['items'] ?? [] as $item) {
            if (!$itemObj = Class2001::getItemById($item['id'])) continue;

            $product = [
                'name' => $itemObj->name,
                'price' => $item['price'],
                'key' => $itemObj->art ?: $itemObj->id,
            ];

            if ($itemObj->ves) $product['weight'] = $itemObj->ves;
            if ($itemObj->height) $product['height'] = $itemObj->height;
            if ($itemObj->width) $product['width'] = $itemObj->width;
            if ($itemObj->length) $product['length'] = $itemObj->length;
            
            if (!empty($item['count'])) $product['count'] = $item['count'];

            $products[] = $product;
        }
        return $products;
    }

    public function getServices()
    {
        return [];   
    }

    public function getOrderNumber()
    {
        return $this->order['Message_ID'];
    }

    protected function getOrderList()
    {
        if (!isset($this->orderList)) {
            $this->orderList = orderArray($this->order['orderlist']) ?: [];
        }
        return $this->orderList;
    }

    protected function getCustomForm()
    {
        if (!isset($this->customForm)) {
            $this->customForm = orderArray($this->order['customf']) ?: [];
        }
        return $this->customForm;
    }
}