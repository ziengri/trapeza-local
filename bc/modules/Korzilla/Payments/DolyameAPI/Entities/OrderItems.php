<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Entities;

use App\modules\Korzilla\Payments\DolyameAPI\Contracts\Arrayable;

class OrderItems implements Arrayable
{
    /**
     * @var array|\App\modules\Korzilla\Payments\DolyameAPI\Entities\Item[]
     */
    private  $items = [];

    public static function fromArray(array $arr): OrderItems
    {
        $obj = new self();

        foreach ($arr as $item) {
            $obj->addItem(
                Item::fromArray($item)
            );
        }

        return $obj;
    }

    public function addItem(Item $item): OrderItems
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @return array|\App\modules\Korzilla\Payments\DolyameAPI\Entities\Item[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array|\App\modules\Korzilla\Payments\DolyameAPI\Entities\Item[] $items
     * @return OrderItems
     */
    public function setItems(array $items): OrderItems
    {
        $this->items = $items;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return array_map(
            function (Item $item) {
                return $item->toArray();
            },
            $this->items
        );
    }
}