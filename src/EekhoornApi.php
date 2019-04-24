<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk;

use Eekhoorn\PhpSdk\Contracts\EekhoornApiInterface;
use Eekhoorn\PhpSdk\Exceptions\RequestException;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class EekhoornApi implements EekhoornApiInterface
{
    /** @var string */
    protected $apiUrl;

    /** @var HttpClient */
    protected $httpClient;

    /** @var CacheInterface */
    protected $cache;

    /**
     * @param string              $apiUrl
     * @param HttpClient|null     $httpClient
     * @param CacheInterface|null $cache
     */
    public function __construct(
        string $apiUrl,
        HttpClient $httpClient = null,
        CacheInterface $cache = null
    ) {
        if ($httpClient === null) {
            $httpClient = HttpClientDiscovery::find();
        }

        $this->setCache($cache ?: new FilesystemCache('de-eekhoorn-sdk'));

        $this
            ->setApiUrl($apiUrl)
            ->setHttpClient($httpClient);
    }

    /**
     * @param string $apiUrl
     * @return $this
     */
    public function setApiUrl(string $apiUrl): EekhoornApiInterface
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
     * @param HttpClient $httpClient
     * @return $this
     */
    public function setHttpClient(HttpClient $httpClient): EekhoornApiInterface
    {
        $this->httpClient = $httpClient;

        return $this;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache): EekhoornApiInterface
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface
    {
        return $this->cache;
    }

    /**
     * @param string $uri
     * @param string $method
     * @param array  $body
     * @param array  $headers
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     * @throws RequestException
     */
    public function doRequest($uri, $method = 'get', array $body = [], array $headers = []): ResponseInterface
    {
        if (strpos($uri, $this->apiUrl) !== 0) {
            $uri = $this->apiUrl . $uri;
        }

        $request = $this->buildRequest($uri, $method, $body, $headers);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 300) {
            throw new RequestException($request, $response);
        }

        return $response;
    }

    /**
     * @param              $uri
     * @param string       $method
     * @param array|string $body
     * @param array        $headers
     * @return RequestInterface
     */
    private function buildRequest($uri, $method = 'GET', $body = '', array $headers = []): RequestInterface
    {
        if (is_array($body)) {
            $body = json_encode($body);
        }
        $request = new Request($method, $uri, $headers, $body);

        return $request;
    }
}