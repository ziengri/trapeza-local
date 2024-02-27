<?php

namespace App\modules\Korzilla\RKeeper\Values\Inputs;
use App\modules\Ship\Parent\Inputs\Input;

class RKeeperAuthInput extends Input
{
    private $catalogueId;

    private $clientId;

    private $clientSecret;

    public function __construct($catalogueId, $clientId, $clientSecret)
    {
        $this->catalogueId = $catalogueId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }


    public function getCatalogueId(){
        return $this->catalogueId;
    }
    public function getClientId(){
        return $this->clientId;
    }
    public function getClientSecret(){
        return $this->clientSecret;
    }
}
