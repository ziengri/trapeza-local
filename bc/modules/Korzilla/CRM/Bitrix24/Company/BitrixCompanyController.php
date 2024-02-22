<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company;

use App\modules\Korzilla\CRM\Bitrix24\Company\Request\Request;

class BitrixCompanyController
{
    public function __construct($bitrixCompany, $bitrixUser, $bitrixKey, $bitrixResponsibleID, $data, $products)
    {
        $this->company = $bitrixCompany;
        $this->user = $bitrixUser;
        $this->key = "https://{$this->company}.bitrix24.ru/rest/{$this->user}/{$bitrixKey}/";
        $this->responsibleID = $bitrixResponsibleID;
        $this->data = $data;
        $this->products = $products;

        

        $this->setRequestController();

        $this->requestCompanyID = $this->companyExist();
        $result = $this->orderOrForm($this->data, $this->products);
        $method = $result['method'];
        $this->$method($result['data']);
    }

    /** 
     * Проверяет существует ли компания
     * @var int $ID
     * @return int
     */
    public function companyExist()
    {
        $response = json_decode($this->request->doRequest(new \App\modules\Korzilla\CRM\Bitrix24\Company\Request\Company\RequestCompanyExist()));

        $companyName = !empty($this->data['NAME']) ? $this->data['NAME'] : (!empty($this->data['PHONE']) ? $this->data['PHONE'] : ($this->data['EMAIL'] ? $this->data['EMAIL'] : $this->data['TITLE']));
        $companyPhone = !empty($this->data['PHONE']) ? ["type" => "PHONE", "contact" => $this->data['PHONE']] : NULL;
        $companyEmail = !empty($this->data['EMAIL']) ? ["type" => "EMAIL", "contact" => $this->data['EMAIL']] : NULL;
        $companyContacts = [$companyEmail, $companyPhone];
        
        foreach ($response->result as $company) {
            if ($company->HAS_PHONE == "Y") {
                foreach($company->PHONE as $phone) {
                    if ($phone->VALUE == $companyPhone['contact']) {
                        return $company->ID;
                    }
                }
            }
            
            if ($company->HAS_EMAIL == "Y") {
                foreach($company->EMAIL as $email) {
                    if ($email->VALUE == $companyEmail['contact']) {
                        return $company->ID;
                    }
                }
            }
        }

        return $this->createCompany($companyName, $companyContacts);
    }

    /** 
     * Создает компанию и возвращает ID компании 
     * @var int $ID
     * @return int
    */
    public function createCompany($companyName, $companyPhone)
    {
        $requestUrl = new \App\modules\Korzilla\CRM\Bitrix24\Company\Request\Company\RequestCompanyCreate($companyName, $companyPhone, $this->responsibleID);

        $response = json_decode($this->request->doRequest($requestUrl));
        
        return $response->result;
    }

    public function createDeal($data)
    {
        new \App\modules\Korzilla\CRM\Bitrix24\Company\Export\ExportDealProducts($this->key,$this->requestCompanyID, $this->responsibleID, $data['info'], $data['products']);
    }

    public function createComment($data)
    {
        new \App\modules\Korzilla\CRM\Bitrix24\Company\Export\ExportComment($this->key,$this->requestCompanyID, $this->responsibleID, $data['info']);
    }

    public function orderOrForm()
    {
        if (!empty($this->products)) {
            return ["method" => "createDeal", "data" => ["info" => $this->data, "products" => $this->products]];
        }

        return ["method" => "createComment", "data" => ["info" => $this->data]];
    }

    public function setRequestController()
    {
        $this->request = new Request($this->key);
    }
}