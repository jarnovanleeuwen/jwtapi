<?php
namespace JwtApi\Client;

use Firebase\JWT\JWT;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use JwtApi\Client\Exceptions\AccessTokenException;
use JwtApi\Client\Exceptions\RequestException;

class Client
{
    const VERSION = '0.3.1';
    const DEFAULT_HASH_ALGORITHM = 'RS256';
    const HEADER_API_KEY = 'API-Key';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var array
     */
    private $claims;

    /**
     * @var string
     */
    private $hashAlgorithm;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $privateKey;

    public function __construct(string $apiUrl, string $apiKey, array $claims = [])
    {
        $this->apiUrl = $apiUrl;
        $this->apiKey = $apiKey;
        $this->claims = $claims;

        $this->setHttpClient(new HttpClient([
            'base_uri' => $apiUrl,
            'http_errors' => false,
            'headers' => ['User-Agent' => static::version()]
        ]));
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getClaims(): array
    {
        return [];
    }

    public function setClaims(array $claims): void
    {
        $this->claims = $claims;
    }

    public function setHttpClient(HttpClient $httpClient): void
    {
        $this->httpClient = $httpClient;
    }

    public function loadPrivateKey(string $path, string $hashAlgorithm = self::DEFAULT_HASH_ALGORITHM): void
    {
        $this->setPrivateKey(file_get_contents($path), $hashAlgorithm);
    }

    public function setPrivateKey(string $privateKey, string $hashAlgorithm = self::DEFAULT_HASH_ALGORITHM): void
    {
        $this->privateKey = $privateKey;
        $this->hashAlgorithm = $hashAlgorithm;
    }

    protected function createAccessToken(): string
    {
        if ($this->privateKey === null) {
            throw new AccessTokenException("Cannot create Access Token because no Private Key has been set.");
        }

        return JWT::encode(array_merge([
            'iat' => time(),
            'iss' => static::version()
        ], $this->claims), $this->privateKey, $this->hashAlgorithm);
    }

    protected function getRequestOptions(Request $request): array
    {
        $options = [
            RequestOptions::HEADERS => [
                static::HEADER_API_KEY => $this->apiKey,
                'Authorization' => "Bearer {$this->createAccessToken()}",
                'Accept' => 'application/json'
            ]
        ];

        if ($parameters = $request->getParameters()) {
            $options[RequestOptions::QUERY] = $parameters;
        }

        if ($payload = $request->getPayload()) {
            $options[RequestOptions::JSON] = $payload;
        }

        return array_merge_recursive($options, $request->getRequestOptions());
    }

    /**
     * @throws RequestException
     */
    public function send(Request $request): Response
    {
        try {
            $response = $this->httpClient->request(
                $request->getMethod(),
                $request->getUri(),
                $this->getRequestOptions($request)
            );

            return new Response($response);
        } catch (ClientException $exception) {
            throw new RequestException($exception->getMessage());
        }
    }

    public static function version(): string
    {
        return 'JwtApi/'.self::VERSION;
    }
}
