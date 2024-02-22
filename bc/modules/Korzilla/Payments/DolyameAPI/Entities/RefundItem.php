<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Entities;

use App\modules\Korzilla\Payments\DolyameAPI\Contracts\Arrayable;

class RefundItem implements Arrayable
{
    /**
     * @var string
     */
    private $refundId;

    /**
     * @var float
     */
    private $refundedAmount;

    /**
     * @var float
     */
    private $refundedPrepaidAmount;

    /**
     * @var \App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderItems|null
     */
    private $returnedItems = null;

    /**
     * Статус возврата.
     * pending - возврат принят и находится в обработке,
     * processed - возврат обработан, денежные средства возмещены клиенту
     * @var string
     */
    private $status;

    public function __construct(string $refundId, float $refundedAmount, float $refundedPrepaidAmount, string $status)
    {

        $this->refundId = $refundId;
        $this->refundedAmount = $refundedAmount;
        $this->refundedPrepaidAmount = $refundedPrepaidAmount;
        $this->status = $status;
    }

    public static function fromArray(array $arr): RefundItem
    {
        $obj = new self(
            $arr['refund_id'],
            floatval($arr['refunded_amount']),
            floatval($arr['refunded_prepaid_amount']),
            $arr['status']
        );

        if (isset($arr['returned_items'])) {
            $obj->setReturnedItems(
                OrderItems::fromArray($arr['returned_items'])
            );
        }

        return $obj;
    }

    /**
     * Идентификатор возврата в Долями
     * @return string
     */
    public function getRefundId(): string
    {
        return $this->refundId;
    }

    /**
     * Сумма возврата
     * @return float
     */
    public function getRefundedAmount(): float
    {
        return $this->refundedAmount;
    }

    /**
     * Сумма возвращенной предоплаты
     * @return float
     */
    public function getRefundedPrepaidAmount(): float
    {
        return $this->refundedPrepaidAmount;
    }

    /**
     * Товары, которые были возвращены
     * @return \App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderItems|null
     */
    public function getReturnedItems(): ?OrderItems
    {
        return $this->returnedItems;
    }

    /**
     * @param \App\modules\Korzilla\Payments\DolyameAPI\Entities\OrderItems|null $returnedItems
     * @return RefundItem
     */
    public function setReturnedItems(?OrderItems $returnedItems): RefundItem
    {
        $this->returnedItems = $returnedItems;
        return $this;
    }

    /**
     * Статус возврата.
     * pending - возврат принят и находится в обработке,
     * processed - возврат обработан, денежные средства возмещены клиенту
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'refund_id' => $this->refundId,
            'refunded_amount' => $this->refundedAmount,
            'refunded_prepaid_amount' => $this->refundedPrepaidAmount,
            'returned_items' => $this->returnedItems ? $this->returnedItems->toArray() : null,
            'status' => $this->status,
        ];
    }
}