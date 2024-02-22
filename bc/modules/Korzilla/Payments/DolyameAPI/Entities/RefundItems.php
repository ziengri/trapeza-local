<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Entities;

use App\modules\Korzilla\Payments\DolyameAPI\Contracts\Arrayable;

class RefundItems implements Arrayable
{
    /**
     * @var array|\App\modules\Korzilla\Payments\DolyameAPI\Entities\RefundItem[]
     */
    private $items = [];

    public static function fromArray(array $arr): RefundItems
    {
        $obj = new self();

        foreach ($arr as $item) {
            $obj->addItem(
                RefundItem::fromArray($item)
            );
        }

        return $obj;
    }

    public function addItem(RefundItem $item): RefundItems
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @return array|\App\modules\Korzilla\Payments\DolyameAPI\Entities\RefundItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array|\App\modules\Korzilla\Payments\DolyameAPI\Entities\RefundItem[] $items
     * @return \App\modules\Korzilla\Payments\DolyameAPI\Entities\RefundItems
     */
    public function setItems(array $items): RefundItems
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
            function (RefundItem $item) {
                return $item->toArray();
            },
            $this->items
        );
    }
}