<?php

declare(strict_types=1);

namespace Chilipublish\Sdk\Rest\Endpoint\Resources;

use Chilipublish\Sdk\Rest\Endpoint\Endpoint;
use Psr\Http\Message\ResponseInterface;

final class CreateResourceItem implements Endpoint
{
    private $apiKey;
    private $fileName;
    private $path;
    private $fileContent;
    private $resourceType;

    public function __construct(string $apiKey, string $resourceType, string $filename, string $path, string $fileContent)
    {
        $this->apiKey = $apiKey;
        $this->filename = $filename;
        $this->path = $path;
        $this->fileContent = $fileContent;
        $this->resourceType = $resourceType;
    }

    public function getBody($streamFactory = null): array
    {

    }

    public function getQueryString(): string
    {
        return http_build_query([
            'newName' => $this->fileName,
            'folderPath' => $this->path,
        ]);
    }

    public function getUri(): string
    {
        return str_replace('{resourceType}',$this->resourceType,'resources/{resourceType}/items');
    }

    public function getMethod(): string
    {
        return 'POST';
    }

    public function getHeaders(array $baseHeaders = []): array
    {
        return $baseHeaders + [
            'Accept' => 'application/xml',
            'Content-Type' => 'application/json',
            'API-KEY' => $this->apiKey,
        ];
    }

    public function getAuthenticationScopes(): array
    {

    }

    public function parseResponse(ResponseInterface $response)
    {

    }
}