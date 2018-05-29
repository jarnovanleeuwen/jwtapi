<?php
namespace Examples\ClientServer;

use JwtApi\Client\Request as BaseRequest;

class Request extends BaseRequest
{
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function getUri(): string
    {
        return 'server.php';
    }

    public function getRequestOptions(): ?array
    {
        return ['debug' => true];
    }
}
