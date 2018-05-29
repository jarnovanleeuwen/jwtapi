<?php
namespace JwtApi\Client\Exceptions;

use Exception;

class RequestException extends ClientException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
