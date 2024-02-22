<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company\Export;

use App\modules\Korzilla\CRM\Bitrix24\Company\Request\Request;

/** Создает сделку с товарами и суммой на основе заказа */
class ExportDealProducts
{
    public function __construct($restApiUrl, $companyID, $reponsibleID, $info, $products)
    {
        $this->setRequest($restApiUrl);
        $this->companyID = $companyID;
        $this->reponsibleID = $reponsibleID;
        $this->info = $info;
        $this->products = $products;
        $this->dealComment = $this->info['COMMENTS'];

        $this->export();
    }

    public function export()
    {
        $this->dealSum = $this->getProductsSum();

        $this->requestDealProducts = new \App\modules\Korzilla\CRM\Bitrix24\Company\Request\Deal\RequestDealProducts($this->companyID, $this->reponsibleID);

        $this->dealID = $this->createDeal();

        $this->createProductsRows();
    }

    /** 
     * Создает сделку, возвращает ID созданной сделки
     * @return int
     */
    public function createDeal()
    {
        $this->requestDealProducts->setRequestUrl(
            $this->requestDealProducts->getMethodDeal(),
            $this->requestDealProducts->getQueryCreatingDeal($this->info['TITLE'], $this->dealSum, $this->dealComment)
        );

        $response = json_decode($this->request->doRequest($this->requestDealProducts));
        
        return $response->result;
    }

    public function createProductsRows()
    {
        $this->requestDealProducts->setRequestUrl(
            $this->requestDealProducts->getMethodProducts(),
            $this->requestDealProducts->getQueryProductsRows($this->dealID, $this->products)
        );

        $response = $this->request->doRequest($this->requestDealProducts);
        
        return $response;
    }

    public function getProductsSum()
    {
        $sum = 0;
        
        foreach ($this->products as $product) {
            $sum += $product['price'] * $product['count'];
        }

        return $sum;
    }

    public function setRequest($restApiUrl)
    {
        $this->request = new Request($restApiUrl);
    }
}