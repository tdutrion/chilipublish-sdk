<?php

declare(strict_types=1);

namespace Chilipublish\Sdk\Rest;

use Chilipublish\Sdk\Rest\Endpoint\Endpoint;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class Client
{
    public const FETCH_RESPONSE = 'response';
    public const FETCH_OBJECT = 'object';
    protected $httpClient;

    /**
     * @var RequestFactoryInterface
     */
    protected $requestFactory;

    /**
     * @var StreamFactoryInterface
     */
    protected $streamFactory;

    public function __construct(
        ClientInterface $httpClient,
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory
    ) {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
    }

    public function executeEndpoint(Endpoint $endpoint, string $fetch = self::FETCH_OBJECT)
    {
        [$bodyHeaders, $body] = $endpoint->getBody($this->streamFactory);
        $queryString = $endpoint->getQueryString();
        $uriGlue = false === strpos($endpoint->getUri(), '?') ? '?' : '&';
        $uri = $queryString !== '' ? $endpoint->getUri().$uriGlue.$queryString : $endpoint->getUri();
        $uri = 'https://cp-ynl-789.chili-publish.online/rest-api/v1/'.$uri;
        $request = $this->requestFactory->createRequest($endpoint->getMethod(), $uri);

        if ($body) {
            if ($body instanceof StreamInterface) {
                $request = $request->withBody($body);
            } elseif (\is_resource($body)) {
                $request = $request->withBody($this->streamFactory->createStreamFromResource($body));
            } elseif (is_file($body)) {
                $request = $request->withBody($this->streamFactory->createStreamFromFile($body));
            } else {
                $request = $request->withBody($this->streamFactory->createStream($body));
            }
        }

        foreach ($endpoint->getHeaders($bodyHeaders) as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if (\count($endpoint->getAuthenticationScopes()) > 0) {
            $scopes = [];
            foreach ($endpoint->getAuthenticationScopes() as $scope) {
                $scopes[] = $scope;
            }
            $request = $request->withHeader(AuthenticationRegistry::SCOPES_HEADER, $scopes);
        }

        return $endpoint->parseResponse($this->httpClient->sendRequest($request), $fetch);
    }

    public static function create($httpClient = null, array $additionalPlugins = array())
    {
        if (null === $httpClient) {
            $httpClient = \Http\Discovery\Psr18ClientDiscovery::find();
            $plugins = array();
            if (count($additionalPlugins) > 0) {
                $plugins = array_merge($plugins, $additionalPlugins);
            }
            $httpClient = new \Http\Client\Common\PluginClient($httpClient, $plugins);
        }
        $requestFactory = \Http\Discovery\Psr17FactoryDiscovery::findRequestFactory();
        $streamFactory = \Http\Discovery\Psr17FactoryDiscovery::findStreamFactory();

        return new static($httpClient, $requestFactory, $streamFactory);
    }
}
