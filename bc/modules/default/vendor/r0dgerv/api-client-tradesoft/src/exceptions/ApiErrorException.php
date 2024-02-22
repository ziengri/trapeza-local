<?php
namespace R0dgerV\ApiClientTradesoft\exceptions;

use DomainException;

/**
 * Class ApiErrorException
 * @package R0dgerV\ApiClientTradesoft\exceptions
 */
class ApiErrorException extends DomainException
{

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}