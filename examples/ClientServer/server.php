<?php
namespace Examples\ClientServer;

require_once "../../vendor/autoload.php";

use JwtApi\Server\RequestParser;
use JwtApi\Server\Exceptions\RequestException;
use Symfony\Component\HttpFoundation\Request;

$requestParser = new RequestParser(function (string $apiKey): ?string {
    // Lookup and return public key associated with $apiKey
    if ($apiKey == "MR4CkRb4pm7yFpVOVL6jfJj5JClx81p3") {
        return file_get_contents("../public_rsa.pem");
    }
    return null;
});

$result = [];

try {
    $requestParser->setRequest($request = Request::createFromGlobals());
    $requestParser->verify();

    // The request is verified. The request:
    // - contains a valid API key
    // - is signed with the corresponding private key
    // - was signed less than 60 seconds ago
    $result = [
        'status' => 'success',
        'data' => [
            'claims' => $requestParser->getClaims(),
            'random' => $request->get('random'),
            'method' => $request->getMethod()
        ]
    ];
} catch (RequestException $exception) {
    $result = [
        'status' => 'error',
        'code' => $exception->getCode(),
        'message' => $exception->getMessage()
    ];
}

header('Content-Type: application/json');
print json_encode($result);
