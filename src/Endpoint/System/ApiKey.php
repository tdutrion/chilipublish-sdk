<?php

declare(strict_types=1);

namespace Chilipublish\Sdk\Rest\Endpoint\System;

use Chilipublish\Sdk\Rest\Endpoint\Endpoint;
use Exception;
use Psr\Http\Message\ResponseInterface;

final class ApiKey implements Endpoint
{
    private $environmentNameOrURL;
    private $body;

    public function __construct(string $environmentNameOrURL, string $userName, string $password)
    {
        $this->environmentNameOrURL = $environmentNameOrURL;
        $this->body = (object)[
            'userName' => $userName,
            'password' => $password,
        ];
    }

    public function getQueryString(): string
    {
        return http_build_query([
            'environmentNameOrURL' => $this->environmentNameOrURL,
        ]);
    }

    public function getHeaders(array $baseHeaders = []): array
    {
        return $baseHeaders + [
            'Accept' => 'application/xml',
        ];
    }

    public function getAuthenticationScopes(): array
    {
        return [];
    }

    public function parseResponse(ResponseInterface $response)
    {
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299 || !str_starts_with($response->getHeader('Content-Type')[0] ?? '', 'application/xml')) {
            throw new \Exception();
        }
        $output = new \SimpleXMLElement($response->getBody()->getContents());

        if (!isset($output['succeeded']) || 'true' !== (string) $output['succeeded']) {
            throw new Exception();
        }

        if (!isset($output['key'])) {
            throw new \Exception();
        }

        return (string) $output['key'];
    }

    public function getMethod() : string
    {
        return 'POST';
    }

    public function getUri() : string
    {
        return 'system/apikey';
    }

    public function getBody($streamFactory = null) : array
    {
        return [['Content-Type' => ['application/json']], json_encode($this->body)];
    }
}
