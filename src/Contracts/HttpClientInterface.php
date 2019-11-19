<?php

namespace DennisKoster\HttpClient\Contracts;

use DennisKoster\HttpClient\Enums\CacheDurationsEnum;
use DennisKoster\HttpClient\Enums\HttpMethodsEnum;
use Http\Client\HttpClient;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

interface HttpClientInterface
{
    /**
     * @param string $apiUrl
     * @return $this
     */
    public function setApiUrl(string $apiUrl): self;

    /**
     * @return string
     */
    public function getApiUrl(): string;

    /**
     * @param HttpClient $httpClient
     * @return $this
     */
    public function setHttpPlugHttpClient(HttpClient $httpClient): self;

    /**
     * @return HttpClient
     */
    public function getHttpPlugHttpClient(): HttpClient;

    /**
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache): self;

    /**
     * @return CacheInterface
     */
    public function getCache(): CacheInterface;

    /**
     * @param string $uri
     * @param string $method
     * @param array  $body
     * @param array  $headers
     * @param int    $ttl
     * @return ResponseInterface
     */
    public function doRequest(
        $uri,
        $method = HttpMethodsEnum::GET,
        array $body = [],
        array $headers = [],
        $ttl = CacheDurationsEnum::DURATION_10_MIN
    ): ResponseInterface;

}
