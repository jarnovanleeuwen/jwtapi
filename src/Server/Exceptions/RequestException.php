<?php
namespace JwtApi\Server\Exceptions;

use Exception;

class RequestException extends ServerException
{
    const UNRESOLVED_PUBLIC_KEY = 100;
    const INVALID_TOKEN = 101;
    const MISSING_API_KEY = 102;
    const MISSING_BEARER_TOKEN = 103;
    const UNVERIFIED = 104;
    
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidToken(string $message = "Could not decode Bearer token"): self
    {
        return new static($message, static::INVALID_TOKEN);
    }

    public static function missingApiKey(string $message = "No API key found on request"): self
    {
        return new static($message, static::MISSING_API_KEY);
    }

    public static function missingBearerToken(string $message = "No Bearer token found on request"): self
    {
        return new static($message, static::MISSING_BEARER_TOKEN);
    }

    public static function unresolvedPublicKey(string $message = "Could not resolve public key"): self
    {
        return new static($message, static::UNRESOLVED_PUBLIC_KEY);
    }

    public static function unverified(string $message = "Token could not be verified"): self
    {
        return new static($message, static::UNVERIFIED);
    }
}
