<?php
namespace Examples\ClientServer;

require_once "../../vendor/autoload.php";

use JwtApi\Client\Client;

$apiKey = "MR4CkRb4pm7yFpVOVL6jfJj5JClx81p3";

$client = new Client(
    "http://localhost/jwtapi/examples/ClientServer/",
    $apiKey,
    [
        'extra-claim' => 'IAmUntampered'
    ]
);

$client->loadPrivateKey("../private_rsa.pem");

$request = new Request(['random' => random_int(0, PHP_INT_MAX)]);

print '<pre>';
$response = $client->send($request);
print_r($response->getData());
print '</pre>';
