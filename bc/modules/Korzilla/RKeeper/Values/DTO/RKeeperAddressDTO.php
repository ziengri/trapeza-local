<?php

namespace App\modules\Korzilla\RKeeper\Values\DTO;

class RKeeperAddressDTO
{
    /** @var string  */
    public $street;

    /** @var string  */
    public $cityName;

    /** @var int  */
    public $floor;

    /** @var string  */
    public $houseNumber;

    /** @var string  */
    public $apartmentNumber;

    /** @var string  */
    public $fullAddress;

    /** @var string  */
    public $intercom;

    public function toArray() : array {
        $array = [];
        foreach ($this as $key => $value) {
            $array[$key] = $value;
        }
        return $array;
    }

}