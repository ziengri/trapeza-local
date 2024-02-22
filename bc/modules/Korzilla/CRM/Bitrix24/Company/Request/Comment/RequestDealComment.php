<?php

namespace App\modules\Korzilla\CRM\Bitrix24\Company\Request\Comment;

class RequestDealComment
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

    public function getMethodDealComment()
    {
        return "crm.livefeedmessage.add";
    }

    public function getQueryDealComment($postTitle, $message, $dealID)
    {
        $data = [
            "fields" => [
                "POST_TITLE" => "{$postTitle}",
                "MESSAGE" => "{$message}",
                "OPENED" => "Y",
                "BEGINDATE" => "{$this->beginDate}",
                "ENTITYTYPEID" => 2,
                "ENTITYID" => "{$dealID}",
            ],
            "params" => [
                "REGISTER_SONET_EVENT" => "Y",
            ],
        ];

        return http_build_query($data);
    }

    public function getQueryCreatingDeal($dealTitle)
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
            ],
            "params" => [
                "REGISTER_SONET_EVENT" => "Y",
            ],
        ];

        return http_build_query($data);
    }
}