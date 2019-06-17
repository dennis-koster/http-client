<?php

namespace Eekhoorn\PhpSdk;

use Eekhoorn\PhpSdk\Contracts\JsonApiSdkInterface;
use Eekhoorn\PhpSdk\Exceptions\RequestException;
use function GuzzleHttp\Psr7\parse_response;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\str;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class JsonApiSdk implements JsonApiSdkInterface
{
    /** @var string */
    protected $apiUrl;

    /** @var HttpClient|null */
    protected $httpClient;

    /** @var CacheInterface|null */
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
    public function setApiUrl(string $apiUrl): JsonApiSdkInterface
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
    public function setHttpClient(HttpClient $httpClient): JsonApiSdkInterface
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
    public function setCache(CacheInterface $cache): JsonApiSdkInterface
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
     * @param int    $ttl
     * @return ResponseInterface
     * @throws RequestException
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function doRequest(
        $uri,
        $method = self::METHOD_GET,
        array $body = [],
        array $headers = [],
        $ttl = self::TTL_10MIN
    ): ResponseInterface {
        if (strpos($uri, $this->apiUrl) !== 0) {
            $uri = $this->apiUrl . $uri;
        }

        // Attempt to fetch a response from cache
        $cacheKey = sha1(http_build_query($headers) . $uri);
        if (($response = $this->cache->get($cacheKey)) && strtolower($method) === 'get' && $ttl !== 0) {
            return parse_response($response);
        }

        $request  = $this->buildRequest($uri, $method, $body, $headers);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() >= 300) {
            throw new RequestException($request, $response);
        }

        // Store the response in cache
        if (strtolower($method) === 'get' && $ttl !== 0) {
            $this->cache->set($cacheKey, str($response), $ttl);
        }

        return $response;
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @return array
     */
    protected function buildGetParameters(
        int $page = 1,
        int $pageSize = 100,
        array $filters = [],
        array $includes = []
    ): array {
        $body = [
            'page' => [
                'number' => $page,
                'size'   => $pageSize,
            ],
        ];
        if ( ! empty($filters)) {
            $body['filter'] = $filters;
        }

        if ( ! empty($includes)) {
            $body['includes'] = $includes;
        }

        return $body;
    }

    /**
     * @param              $uri
     * @param string       $method
     * @param array        $body
     * @param array        $headers
     * @return RequestInterface
     */
    protected function buildRequest($uri, $method = self::METHOD_GET, array $body = [], array $headers = []): RequestInterface
    {
        if ($method === self::METHOD_GET && ! empty($body)) {
            $uri  .= "?" . http_build_query($body);
            $body = '';
        }

        if (is_array($body)) {
            $body = json_encode($body);
        }

        $request = new Request($method, $uri, $headers, $body);

        return $request;
    }
}
