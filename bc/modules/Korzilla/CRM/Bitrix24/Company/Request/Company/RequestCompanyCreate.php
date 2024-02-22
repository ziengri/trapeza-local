<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company\Request\Company;

class RequestCompanyCreate
{
    public function __construct($title, $contact = [], $respID)
    {
        $this->method = $this->getMethod();
        $this->query = $this->getQuery($title, $contact, $respID);
        
        $this->request = $this->setRequestUrl();
    }

    public function setRequestUrl()
    {
        return "{$this->method}?{$this->query}";
    }

    public function getMethod()
    {
        return "crm.company.add";
    }

    public function getQuery($title, $contacts = [], $respID)
    {
        
        $data = ["fields" => [
                "TITLE" => "{$title}",
                "COMPANY_TYPE" => "CUSTOMER",
                "ASSIGNED_BY_ID" => "{$respID}",
                "OPENED" => "Y",
            ],
            "params" => [
                "REGISTER_SONET_EVENT" => "Y"
            ],
        ];
        foreach($contacts as $contact) {

            if (!empty($contact['contact']) && !empty($contact['type'])) {
                $data['fields'][$contact['type']][0] = ["VALUE" => "{$contact['contact']}", "VALUE_TYPE" => "WORK"];
            }
        }

        return http_build_query($data);
    }
}