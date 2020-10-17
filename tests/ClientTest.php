<?php

declare(strict_types=1);

namespace Chilipublish\Tests\Sdk\Rest;

use Chilipublish\Sdk\Rest\Client;
use Chilipublish\Sdk\Rest\Endpoint\System\ApiKey;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

class ClientTest extends TestCase
{
    public function test()
    {
        $httpClient = HttpClient::create();
        $client = Client::create(new Psr18Client($httpClient));
        dd($client->executeEndpoint(new ApiKey()));
    }
}
