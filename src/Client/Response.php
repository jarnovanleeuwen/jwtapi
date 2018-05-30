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

    /**
     * @var ResponseInterface
     */
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        if (($this->data = json_decode($body = $response->getBody(), true)) === null) {
            throw new ResponseException("Could not decode response, expected valid JSON. Raw response: {$body}");
        }
    }

    public function isSuccessful(): bool
    {
        $statusCode = $this->response->getStatusCode();

        return $statusCode >= 200 && $statusCode < 300;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
