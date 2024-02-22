<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Entities;

use DateTime;
use DateTimeInterface;
use InvalidArgumentException;
use App\modules\Korzilla\Payments\DolyameAPI\Contracts\Arrayable;

class PaymentScheduleItem implements Arrayable
{
    private $paymentDate;
    private $amount;
    private $status;

    public function __construct(DateTimeInterface $paymentDate, float $amount, string $status)
    {
        $this->paymentDate = $paymentDate;
        $this->amount = $amount;
        $this->status = $status;
    }

    public static function fromArray(array $arr): PaymentScheduleItem
    {
        $paymentDate = DateTime::createFromFormat(
            'Y-m-d',
            $arr['payment_date']
        );

        if (!$paymentDate) {
            throw new InvalidArgumentException(
                "payment_date should be in 2016-05-23 (Y-m-d) format"
            );
        }
        return new self(
            $paymentDate,
            number_format(floatval($arr['amount']), 2, '.', ''),
            $arr['status']
        );
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getPaymentDate(): DateTimeInterface
    {
        return $this->paymentDate;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'payment_date' => $this->paymentDate->format('Y-m-d'),
            'amount' => number_format( $this->amount , 2, '.', ''),
            'status' => $this->status,
        ];
    }
}