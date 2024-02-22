<?php

namespace App\modules\Korzilla\CRM\Frontpad\Request;

use App\modules\Korzilla\ToolsAssist\Request\Response\ResponseInterface;

class Response implements ResponseInterface
{
    private $requestInfo;
    private $response;
    private $convertedResponse;

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
        return $this->isError() ? null : $this->responseConvertToArray();
    }

    public function isError(): bool
    {
        return $this->isHttpError() || $this->isResponseError();
    }

    public function getErrorMessage(): string
    {
        if ($this->isResponseError()) {
            return $this->responseConvertToArray()['error'] ?? '';
        }

        if ($this->isHttpError()) {
            return $this->response;
        }

        return '';
    }

    public function getErrorCode(): int
    {
        return $this->isHttpError() ? $this->requestInfo['http_code'] : 0;
    }

    private function responseConvertToArray(): array
    {
        return $this->convertedResponse ?? ($this->convertedResponse = json_decode($this->response, true) ?: []);
    }

    private function isHttpError(): bool
    {
        return $this->requestInfo['http_code'] !== 200;
    }

    private function isResponseError(): bool
    {
        return 'error' === ($this->responseConvertToArray()['result'] ?? '');
    }
}