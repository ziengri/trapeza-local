<?php

namespace App\modules\Korzilla\Payments\DolyameAPI\Exceptions;

use Exception;
use Throwable;

class DolyameRequestException extends Exception
{
    private $response = [];

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function withResponse(array $responseJSON, int $code = 400): DolyameRequestException
    {
        $e = new self($responseJSON['message'], $code);
        $e->setResponse($responseJSON);
        return $e;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    public function setResponse(array $responseJSON): DolyameRequestException
    {
        $this->response = $responseJSON;
        return $this;
    }
}