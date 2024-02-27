<?php

namespace App\modules\Korzilla\RKeeper\Values\DTO;

class RKeeperRestaurantDTO
{
    /** @var string */
    public $id;

    /** @var int */
    public $objectId;

    /** @var string */
    public $name;

    /** @var string */
    public $actualAddress;

    /** @var string */
    public $state;

    /** @var float|null */
    public $actualAddressLat = null;

    /** @var float|null */
    public $actualAddressLon = null;

    /** @var string */
    public $city;

    /** @var string */
    public $schedule = "";
}
