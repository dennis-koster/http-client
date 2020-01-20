<?php

namespace DennisKoster\HttpClient;

use DennisKoster\HttpClient\Contracts\HttpClientInterface;
use DennisKoster\HttpClient\Enums\CacheDurationsEnum;
use DennisKoster\HttpClient\Enums\HttpMethodsEnum;
use DennisKoster\HttpClient\Exceptions\RequestException;
use function GuzzleHttp\Psr7\parse_response;
use GuzzleHttp\Psr7\Request;
use function GuzzleHttp\Psr7\str;
use Http\Client\HttpClient as HttpPlugHttpClient;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class HttpClient implements HttpClientInterface
{
    /** @var string */
    protected $apiUrl;

    /** @var HttpPlugHttpClient|null */
    protected $httpClient;

    /** @var CacheInterface|null */
    protected $cache;

    /**
     * @param string                  $apiUrl
     * @param HttpPlugHttpClient|null $httpPlugHttpClient
     * @param CacheInterface|null     $cache
     */
    public function __construct(
        string $apiUrl,
        HttpPlugHttpClient $httpPlugHttpClient = null,
        CacheInterface $cache = null
    ) {
        if ($httpPlugHttpClient === null) {
            $httpPlugHttpClient = HttpClientDiscovery::find();
        }

        $this->setCache($cache ?: new FilesystemCache('dennis-koster-http-client'));

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
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache): HttpClientInterface
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
     * @param array|string  $body
     * @param array  $headers
     * @param int    $ttl
     * @return ResponseInterface
     * @throws RequestException
     * @throws \Http\Client\Exception
     */
    public function doRequest(
        $uri,
        $method = HttpMethodsEnum::GET,
        $body = [],
        array $headers = [],
        $ttl = CacheDurationsEnum::DURATION_10_MIN
    ): ResponseInterface {
        if ( ! empty($this->apiUrl) && strpos($uri, $this->apiUrl) !== 0) {
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
     * @param string $url
     * @param array|string  $body
     * @param array  $headers
     * @return ResponseInterface
     * @throws RequestException
     * @throws \Http\Client\Exception
     */
    public function post(string $url, $body = [], array $headers = []): ResponseInterface
    {
        return $this->doRequest(
            $url,
            HttpMethodsEnum::POST,
            $body,
            $headers
        );
    }

    /**
     * @param string $url
     * @param array  $headers
     * @param int    $ttl
     * @return ResponseInterface
     * @throws RequestException
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function get(string $url, array $headers = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): ResponseInterface
    {
        // Attempt to fetch a response from cache
        $cacheKey = sha1(http_build_query($headers) . $url);
        if ($ttl !== 0 && ($response = $this->cache->get($cacheKey))) {
            return parse_response($response);
        }

        $response = $this->doRequest(
            $url,
            HttpMethodsEnum::GET,
            [],
            $headers,
            $ttl
        );

        if ($ttl === 0) {
            return $response;
        }

        // Store the response in cache
        $this->cache->set($cacheKey, str($response), $ttl);
        return parse_response($this->cache->get($cacheKey));
    }

    /**
     * @param              $uri
     * @param string       $method
     * @param array|string        $body
     * @param array        $headers
     * @return RequestInterface
     */
    protected function buildRequest($uri, $method = HttpMethodsEnum::GET, $body = [], array $headers = []): RequestInterface
    {
        if ($method === HttpMethodsEnum::GET && is_array($body) && ! empty($body)) {
            $uri  .= "?" . http_build_query($body);
            $body = null;
        }

        if (is_array($body)) {
            $body = json_encode($body);
        }

        return new Request($method, $uri, $headers, $body);
    }
}
