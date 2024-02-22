<?php

namespace App\modules\Korzilla\CRM\Planfix\EntityParser;

use nc_Core;

class Order
{
    private $order;
    private $formData;
    private $orderList;
    private $nc_core;

    public function __construct($order)
    {
        $this->order = $order;
        $this->nc_core = nc_Core::get_object();
    }

    public function getClientName()
    {
        return $this->order['fio'] ?: '';
    }
    
    public function getClientPhone()
    {
        return $this->getFormData()['phone']['value'] ?? '';
    }

    public function getClientEmail()
    {
        return $this->getFormData()['email']['value'] ?? '';
    }

    public function getClientComment()
    {
        return $this->getFormData()['comment']['value'] ?? '';
    }

    public function getOrderId()
    {
        return $this->order['Message_ID'];
    }

    public function getOrderSum()
    {
        return $this->_getOrderList()['totaldelsum'] ?? 0;
    }

    public function getOrderList()
    {
        $domain = $this->nc_core->catalogue->get_current('Domain');
        $list = '';
        foreach ($this->_getOrderList()['items'] ?? [] as $product) {
            $productLink = nc_message_link($product['id'], 2001);

            $list .= $list ? '<br />' : '';
            $list .= "<a target=\"_blank\" href=\"//{$domain}{$productLink}\">{$product['name']}</a> - {$product['count']} шт.";
        }

        return $list;
    }

    public function getDeliveryAddress()
    {
        return $this->getFormData()['address']['value'] ?? 'Не указано';
    }

    private function getFormData(): array
    {
        return $this->formData ?? ($this->formData = orderArray($this->order['customf']) ?: []);
    }

    private function _getOrderList(): array
    {
        return $this->orderList ?? ($this->orderList = orderArray($this->order['orderlist']) ?: []);
    }
}