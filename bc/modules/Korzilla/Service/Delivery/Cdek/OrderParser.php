<?php

namespace App\modules\Korzilla\Service\Delivery\Cdek;

class OrderParser implements OrderDataInterface
{
    private $order;
    private $orderList;
    private $customForm;

    /**
     * @param array $order массив заказа из базы
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    public function getTariffCode()
    {
        return $this->getOrderList()['delivery']['assist']['tariff_code'];
    }

    public function getDeliveryPoint()
    {
        return $this->getOrderList()['delivery']['assist']['pvz_code'];
    }

    public function getToLocation()
    {
        $customForm = $this->getCustomForm();
        return [
            'address' => $customForm['address']['value'],
        ];
    }

    private function getOrderList()
    {
        if (!isset($this->orderList)) {
            $this->orderList = orderArray($this->order['orderlist']) ?: [];
        }
        return $this->orderList;
    }

    private function getCustomForm()
    {
        if (!isset($this->customForm)) {
            $this->customForm = orderArray($this->order['customf']) ?: [];
        }
        return $this->customForm;
    }
}