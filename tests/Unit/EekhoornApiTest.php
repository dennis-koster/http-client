<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk\Tests;

use Eekhoorn\PhpSdk\EekhoornApi;
use Eekhoorn\PhpSdk\Exceptions\RequestException;
use Http\Client\HttpClient;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class EekhoornApiTest extends TestCase
{
    /** @var HttpClient|Mockery\MockInterface */
    private $httpClient;

    /** @var EekhoornApi */
    private $sdk;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = Mockery::mock(HttpClient::class);
        $this->sdk = new EekhoornApi('https://api.foobar.com', $this->httpClient);
    }

    /** @test */
    public function testItSetsAnHttpClientThroughTheConstructor()
    {
        $this->assertEquals($this->httpClient, $this->sdk->getHttpClient());
    }

    /** @test */
    public function testItSetsAnHttpClientThroughTheSetter()
    {
        $httpClient = Mockery::mock(HttpClient::class);
        $this->sdk->setHttpClient($httpClient);
        $this->assertEquals($httpClient, $this->sdk->getHttpClient());
    }

    /** @test */
    public function testItSetsAnApiUrlThroughTheConstructor()
    {
        $this->assertEquals('https://api.foobar.com', $this->sdk->getApiUrl());
    }

    /** @test */
    public function testItSetsAnApiUrlThroughTheSetter()
    {
        $this->sdk->setApiUrl('http://api.foo.bar');
        $this->assertEquals('http://api.foo.bar', $this->sdk->getApiUrl());
    }

    /** @test */
    public function testItThrowsARequestExceptionIfTheStatusCodeIsHigherThan300()
    {
        $httpClient = Mockery::mock(HttpClient::class, [
            'sendRequest' => Mockery::mock(ResponseInterface::class, [
                'getStatusCode' => 401,
                'getReasonPhrase' => 'Authorization required'
            ])
        ]);
        $this->sdk->setHttpClient($httpClient);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Authorization required');
        $this->expectExceptionCode(401);
        $this->sdk->doRequest('/bla', 'post');
    }

    /** @test */
    public function testItReturnsAResponse()
    {
        $httpClient = Mockery::mock(HttpClient::class, [
            'sendRequest' => Mockery::mock(ResponseInterface::class, [
                'getStatusCode' => 200
            ])
        ]);
        $this->sdk->setHttpClient($httpClient);
        $response = $this->sdk->doRequest('/bla', 'get');
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}