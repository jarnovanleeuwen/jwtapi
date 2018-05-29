<?php
namespace JwtApi\Client\Exceptions;

use Exception;
use RuntimeException;

abstract class ClientException extends RuntimeException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
