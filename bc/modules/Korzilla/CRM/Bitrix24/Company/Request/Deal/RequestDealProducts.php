<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company\Request\Deal;

class RequestDealProducts
{
    public function __construct($companyID, $responsibleID)
    {
        $this->companyID = $companyID;
        $this->responsibleID = $responsibleID;
        $this->beginDate = date("Y-m-d H:i");
    }

    public function setRequestUrl($method, $query)
    {
        $this->request = "{$method}?{$query}";
    }

    public function getMethodDeal()
    {
        return "crm.deal.add";
    }

    public function getMethodProducts()
    {
        return "crm.deal.productrows.set";
    }

    public function getQueryCreatingDeal($dealTitle, $productsSum, $comment)
    {
        $data = [
            "fields" => [
                "TITLE" => "{$dealTitle}",
                "TYPE_ID" => "GOODS",
                "STAGE_ID" => "NEW",
                "COMPANY_ID" => "{$this->companyID}",
                "OPENED" => "Y",
                "ASSIGNED_BY_ID" => $this->responsibleID,
                "BEGINDATE" => "{$this->beginDate}",
                "OPPORTUNITY" => "{$productsSum}",
            ],
            "params" => [
                "REGISTER_SONET_EVENT" => "Y",
            ],
        ];

        return http_build_query($data);
    }

    public function getQueryProductsRows($dealID, $products)
    {
        $data = [
            "id" => $dealID,
        ];

        foreach($products as $productID => $product) {
            $data['rows'][$productID] = [
                "PRODUCT_NAME" => $product['name'], 
                "PRICE" => $product['price'], 
                "QUANTITY" => $product['count'],
				"MEASURE_CODE"=> getEdzimCode($product['edizm'])
            ];
        }
		file_put_contents("/var/www/krza/data/www/krza.ru/trash/b24.log",print_r($data,1)."\n\n".print_r($products,1),FILE_APPEND);
        return http_build_query($data);
    }
}