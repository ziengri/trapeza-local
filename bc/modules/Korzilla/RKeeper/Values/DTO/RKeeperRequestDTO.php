<?php

namespace App\modules\Korzilla\RKeeper\Values\DTO;

class RKeeperRequestDTO
{   
    /** @var string  */
    public $url;

    /** @var array  */
    public $headers = [];

    /** @var string  */
    public $body;


    /** @var string "GET"|"POST"|"PUT"|"DELETE" */
    public $method = "GET";
}
