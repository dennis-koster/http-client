<?php

namespace DennisKoster\HttpClient;

use DennisKoster\HttpClient\Contracts\HttpClientInterface;
use DennisKoster\HttpClient\Enums\HttpMethodsEnum;
use DennisKoster\HttpClient\Exceptions\RequestException;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient as HttpPlugHttpClient;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpClient implements HttpClientInterface
{
    /** @var string */
    protected $apiUrl;

    /** @var HttpPlugHttpClient|null */
    protected $httpClient;

    /**
     * @param string                  $apiUrl
     * @param HttpPlugHttpClient|null $httpPlugHttpClient
     */
    public function __construct(
        string $apiUrl,
        HttpPlugHttpClient $httpPlugHttpClient = null
    ) {
        if ($httpPlugHttpClient === null) {
            $httpPlugHttpClient = HttpClientDiscovery::find();
        }

        $this
            ->setApiUrl($apiUrl)
            ->setHttpPlugHttpClient($httpPlugHttpClient);
    }

    /**
     * @param string $apiUrl
     * @return $this
     */
    public function setApiUrl(string $apiUrl): HttpClientInterface
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     * @param HttpPlugHttpClient $httpClient
     * @return $this
     */
    public function setHttpPlugHttpClient(HttpPlugHttpClient $httpClient): HttpClientInterface
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @return HttpPlugHttpClient
     */
    public function getHttpPlugHttpClient(): HttpPlugHttpClient
    {
        return $this->httpClient;
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array  $body
     * @param array  $headers
     * @return ResponseInterface
     * @throws RequestException
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function doRequest(
        $uri,
        $method = HttpMethodsEnum::GET,
        array $body = [],
        array $headers = []
    ): ResponseInterface {
        if (strpos($uri, $this->apiUrl) !== 0) {
            $uri = $this->apiUrl . $uri;
        }

        $request  = $this->buildRequest($uri, $method, $body, $headers);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 300) {
            throw new RequestException($request, $response);
        }

        return $response;
    }

    /**
     * @param              $uri
     * @param string       $method
     * @param array        $body
     * @param array        $headers
     * @return RequestInterface
     */
    protected function buildRequest($uri, $method = HttpMethodsEnum::GET, array $body = [], array $headers = []): RequestInterface
    {
        if ($method === HttpMethodsEnum::GET && ! empty($body)) {
            $uri  .= "?" . http_build_query($body);
            $body = null;
        }

        if (is_array($body)) {
            $body = json_encode($body);
        }

        $request = new Request($method, $uri, $headers, $body);

        return $request;
    }
}
