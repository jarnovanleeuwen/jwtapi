<?php
namespace JwtApi\Server;

use Closure;
use DateTime;
use Exception;
use Firebase\JWT\JWT;
use JwtApi\Client\Client;
use JwtApi\Server\Exceptions\RequestException;
use LogicException;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

class RequestParser
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $bearerToken;

    /**
     * @var array
     */
    private $claims;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Closure
     */
    private $publicKeyResolver;

    /**
     * TIll how long after issuance are tokens expected.
     *
     * @var int in seconds
     */
    private $expiration;

    /**
     * Account for clock skew between signing and verifying servers.
     *
     * @var int in seconds
     */
    private $leeway;

    /**
     * Make sure the same timestamp is used for decoding and verifying the token.
     *
     * @var int
     */
    private $timestamp;

    public function __construct(Closure $publicKeyResolver, int $expiration = 60, int $leeway = 0)
    {
        $this->publicKeyResolver = $publicKeyResolver;
        $this->expiration = $expiration;
        $this->leeway = $leeway;
    }

    /**
     * @throws RequestException
     */
    public function setRequest(Request $request, $headerName = Client::HEADER_API_KEY): void
    {
        $this->request = $request;
        $this->apiKey = static::extractApiKey($request, $headerName);
        $this->bearerToken = static::extractBearerToken($request);
        $this->claims = $this->decodeBearerToken();
        $this->timestamp = time();
    }

    /**
     * @throws RequestException
     */
    public function verify(): void
    {
        if (!$this->request) {
            throw new LogicException("No request set");
        }

        if (!is_int($issuedAt = $this->getClaim('iat'))) {
            throw RequestException::unverified("'issued at' claim missing");
        }

        if ($issuedAt > ($this->timestamp + $this->leeway)) {
            throw RequestException::unverified("Token is not valid before " . date(DateTime::ISO8601, $issuedAt));
        }

        if (($this->timestamp - $this->leeway) >= ($issuedAt + $this->expiration)) {
            throw RequestException::unverified("Token is expired");
        }
    }

    /**
     * @throws RequestException
     */
    protected function decodeBearerToken(): array
    {
        JWT::$leeway = $this->leeway;
        JWT::$timestamp = $this->timestamp;

        try {
            return (array) JWT::decode($this->bearerToken, $this->resolvePublicKey(), ['RS256']);
        } catch (RequestException $exception) {
            throw $exception;
        } catch (UnexpectedValueException $exception) {
            throw RequestException::invalidToken($exception->getMessage());
        } catch (Exception $exception) {
            throw RequestException::invalidToken();
        }
    }

    /**
     * @throws RequestException
     */
    protected function resolvePublicKey(): string
    {
        $resolver = $this->publicKeyResolver;

        if (empty($publicKey = $resolver($this->apiKey))) {
            throw RequestException::unresolvedPublicKey();
        }

        return $publicKey;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBearerToken(): string
    {
        return $this->bearerToken;
    }

    public function getClaims(): array
    {
        return $this->claims;
    }

    /**
     * @return mixed
     */
    public function getClaim(string $name, $default = null)
    {
        return $this->claims[$name] ?? $default;
    }

    /**
     * @throws RequestException
     */
    public static function extractApiKey(Request $request, string $headerName): string
    {
        $apiKey = $request->headers->get($headerName);

        if ($apiKey === null) {
            throw RequestException::missingApiKey();
        }

        return $apiKey;
    }

    /**
     * @throws RequestException
     */
    public static function extractBearerToken(Request $request): string
    {
        $header = $request->headers->get('Authorization') ?: '';

        if (substr($header, 0, 7) == 'Bearer ') {
            if ($bearerToken = substr($header, 7)) {
                return $bearerToken;
            }
        }

        throw RequestException::missingBearerToken();
    }
}
