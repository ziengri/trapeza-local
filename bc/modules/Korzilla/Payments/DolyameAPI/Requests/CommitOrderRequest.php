<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Requests;

class CommitOrderRequest extends AbstractOrderRequest
{
    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'prepaid_amount' => $this->prepaidAmount ?? 0,
            'items' => $this->items->toArray(),
        ];
    }
}