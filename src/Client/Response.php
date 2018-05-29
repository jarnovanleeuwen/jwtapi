<?php
namespace JwtApi\Client;

use JwtApi\Client\Exceptions\ResponseException;
use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @var array
     */
    private $data;

    public function __construct(ResponseInterface $response)
    {
        if (($this->data = json_decode($response->getBody(), true)) === null) {
            throw new ResponseException("Could not decode response");
        }
    }

    public function getData(): array
    {
        return $this->data;
    }
}
