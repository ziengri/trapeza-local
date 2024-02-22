<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Contracts;

interface Arrayable
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array;
}