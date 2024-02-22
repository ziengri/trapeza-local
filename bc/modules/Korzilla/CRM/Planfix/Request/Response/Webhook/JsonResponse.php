<?php

namespace App\modules\Korzilla\CRM\Planfix\Request\Response\Webhook;

use App\modules\Korzilla\ToolsAssist\Request\Response\ResponseInterface;

class JsonResponse implements ResponseInterface
{
    private $requestInfo;
    private $response;

    public function setRequestInfo($info)
    {
        $this->requestInfo = $info;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function normalize()
    {
        return json_decode($this->response, true);
    }

    public function isError(): bool
    {
        return 200 !== ($this->requestInfo['http_code'] ?? 0);
    }

    public function getErrorMessage(): string
    {
        return $this->requestInfo['redirect_url'] ? 'редирект - '.$this->requestInfo['redirect_url'] : $this->response;
    }
}