<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company\Request;

class Request
{
    public function __construct($restApiUrl)
    {
        $this->restApiUrl = $restApiUrl;
    }

    public function createRequest($method)
    {
        return "{$this->restApiUrl}{$method->request}";
    }

    public function doRequest($method)
    {
        $requestUrl = $this->createRequest($method);
        
        return file_get_contents($requestUrl);
    }
}