<?php

namespace DennisKoster\HttpClient\Contracts;

use DennisKoster\HttpClient\Enums\HttpMethodsEnum;
use DennisKoster\HttpClient\Exceptions\RequestException;
use Http\Client\HttpClient;
use Psr\Http\Message\ResponseInterface;

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
     * @param string $uri
     * @param string $method
     * @param array  $body
     * @param array  $headers
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     * @throws RequestException
     */
    public function doRequest($uri, $method = HttpMethodsEnum::GET, array $body = [], array $headers = []): ResponseInterface;

}
