<?php

declare(strict_types=1);

namespace Chilipublish\Tests\Sdk\Rest;

use Chilipublish\Sdk\Rest\Client;
use Chilipublish\Sdk\Rest\Endpoint\System\ApiKey;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Psr18Client;

final class ClientTest extends TestCase
{
    private $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = require __DIR__.'/../config/credentials.php';
    }

    public function testGenerateValidToken(): void
    {
        $httpClient = HttpClient::create(['base_uri' => $this->config['valid']['base_uri']]);
        $client = Client::create(new Psr18Client($httpClient));

        $result = $client->executeEndpoint(new ApiKey(
            $this->config['valid']['environmentNameOrURL'],
            $this->config['valid']['userName'],
            $this->config['valid']['password']
        ));

        $this->assertIsString($result);
    }

    public function testRetrieveResources(): void
    {
        $httpClient = HttpClient::create(['base_uri' => $this->config['valid']['base_uri']]);
        $client = Client::create(new Psr18Client($httpClient));
        $apiKey = $client->executeEndpoint(new ApiKey(
            $this->config['valid']['environmentNameOrURL'],
            $this->config['valid']['userName'],
            $this->config['valid']['password']
        ));

        $result = $client->executeEndpoint();
    }
}
