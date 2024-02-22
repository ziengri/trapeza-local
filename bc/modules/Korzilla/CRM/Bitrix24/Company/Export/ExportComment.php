<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company\Export;

use App\modules\Korzilla\CRM\Bitrix24\Company\Request\Request;

/** Создает сделку без товаров и создает комментарий с текстом на основе сообщения с формы */
class ExportComment
{
    public function __construct($restApiUrl, $companyID, $reponsibleID, $info)
    {
        $this->setRequest($restApiUrl);
        $this->companyID = $companyID;
        $this->reponsibleID = $reponsibleID;
        $this->info = $info;

        $this->export();
    }

    public function export()
    {
        $this->requestDealComment = new \App\modules\Korzilla\CRM\Bitrix24\Company\Request\Comment\RequestDealComment($this->companyID, $this->reponsibleID);
        
        $this->dealID = $this->createDeal();

        $this->createComment();
    }

    public function createDeal()
    {
        $this->requestDealComment->setRequestUrl(
            $this->requestDealComment->getMethodDeal(),
            $this->requestDealComment->getQueryCreatingDeal($this->info['TITLE'])
        );

        $response = json_decode($this->request->doRequest($this->requestDealComment));
        
        return $response->result;
    }

    public function createComment()
    {
        $this->requestDealComment->setRequestUrl(
            $this->requestDealComment->getMethodDealComment(),
            $this->requestDealComment->getQueryDealComment($this->info['NAME'], $this->info['COMMENTS'], $this->dealID)
        );

        $response = $this->request->doRequest($this->requestDealComment);
        
        return $response;
    }

    public function setRequest($restApiUrl)
    {
        $this->request = new Request($restApiUrl);
    }
}