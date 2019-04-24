<?php

namespace Eekhoorn\PhpSdk\Contracts;

use Eekhoorn\PhpSdk\Exceptions\RequestException;
use Http\Client\HttpClient;
use Psr\Http\Message\ResponseInterface;

interface EekhoornApiInterface
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
    public function setHttpClient(HttpClient $httpClient): self;

    /**
     * @return HttpClient
     */
    public function getHttpClient(): HttpClient;

    /**
     * @param string $uri
     * @param string $method
     * @param array  $body
     * @param array  $headers
     * @return ResponseInterface
     * @throws \Http\Client\Exception
     * @throws RequestException
     */
    public function doRequest($uri, $method = 'get', array $body = [], array $headers = []): ResponseInterface;
}