<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company\Request\Company;

class RequestCompanyExist
{
    public function __construct()
    {
        $this->getMethod();
        $this->getQuery();

        $this->request = $this->setRequestUrl();
    }

    public function setRequestUrl()
    {
        return "{$this->method}?{$this->query}";
    }

    public function getMethod()
    {
        $this->method = "crm.company.list";
    }

    public function getQuery()
    {
        $this->query = http_build_query([
            "filter" => ["COMPANY_TYPE" => "CUSTOMER"],
            "select" => [ "ID", "TITLE", "HAS_EMAIL", "HAS_PHONE", "PHONE", "EMAIL"],
        ]);
    }
}