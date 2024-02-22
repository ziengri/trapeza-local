<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Entities;

use App\modules\Korzilla\Payments\DolyameAPI\Contracts\Arrayable;

class Item implements Arrayable
{
    private $name;
    private $quantity;
    private $price;
    private $sku = null;

    //*TODO: create Receipt
    // private ?ItemReceipt $receipt = null;

    /**
     * @param string $name
     * @param int $quantity
     * @param float $price
     */
    public function __construct(string $name, int $quantity, float $price, ?string $sku = null)
    {

        $this->name = $name;
        $this->quantity = $quantity;
        $this->price = $price;
        if ($sku !== null) {
            $this->sku = $sku;
        }
    }

    public static function fromArray(array $arr): Item
    {
        $item = new self(
            $arr['name'],
            intval($arr['quantity']),
            number_format(floatval($arr['price']), 2, '.', '')
        );

        if (isset($arr['sku'])) {
            $item->setSku($arr['sku']);
        }

        //*TODO: create Receipt
        // if (isset($arr['receipt'])) {
        //     $item->setReceipt(
        //         ItemReceipt::fromArray($arr['receipt'])
        //     );
        // }

        return $item;
    }

    public function setName(string $name): Item
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @param string|null $sku
     * @return Item
     */
    public function setSku(?string $sku): Item
    {
        $this->sku = $sku;
        return $this;
    }


    //*TODO: create Receipt
    // /**
    //  * @param \VKolegov\DolyameAPI\Entities\ItemReceipt|null $receipt
    //  * @return Item
    //  */
    // public function setReceipt(?ItemReceipt $receipt): Item
    // {
    //     $this->receipt = $receipt;
    //     return $this;
    // }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string|null
     */
    public function getSku(): ?string
    {
        return $this->sku;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {

        $data = [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price' => number_format($this->price, 2, '.', '')
        ];
        if ($this->sku) {
            $data['sku'] = $this->sku;
        }

        //*TODO: create Receipt
        // if ($this->receipt) {
        //     $data['receipt'] = $this->receipt->toArray();
        // }

        return $data;
    }

}