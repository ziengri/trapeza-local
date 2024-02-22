<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Builders;

use App\modules\Korzilla\Payments\DolyameAPI\Entities\Item;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\ClientInfo;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderInfo;
use App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderItems;
use App\modules\Korzilla\Payments\DolyameAPI\Requests\CommitOrderRequest;

class CommitOrderBuilder
{

    private $id;
    private $order;




    /**
     * @param int $id ИД заказа в БЛ
     * @param array $order Позиции заказа в виде массива
     */
    public function __construct(
        int $id,
        array $order
    ) {
        $this->id = $id;
        $this->order = $order;
  
    }


    public function build(): CommitOrderRequest
    {
        $commitOrderRequest = new CommitOrderRequest($this->id, $this->order['totaldelsum'], $this->getOrderItems());
        return $commitOrderRequest;
    }

    private function getOrderItems(): OrderItems
    {

        $orderItems = new OrderItems();

        foreach ($this->order['items'] as $item) {
            $item = new Item($item['name'], $item['count'], number_format($item['price'], 2, '.', ''), $item['art']);
            $orderItems->addItem($item);
        }

        return $orderItems;
    }


}






