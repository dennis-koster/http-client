<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk\Tests\Unit;

use Eekhoorn\PhpSdk\EekhoornApi;
use Eekhoorn\PhpSdk\Exceptions\RequestException;
use Http\Client\HttpClient;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

class EekhoornApiTest extends TestCase
{
    /** @var HttpClient */
    private $httpClient;

    /** @var EekhoornApi */
    private $sdk;

    /** @var CacheInterface */
    private $cache;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = Mockery::mock(HttpClient::class);
        $this->cache = Mockery::mock(CacheInterface::class, [
            'get' => null,
            'set' => true,
        ]);
        $this->sdk = new EekhoornApi('https://api.foobar.com', $this->httpClient, $this->cache);
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
        $this->assertSame($httpClient, $this->sdk->getHttpClient());
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
    public function testItSetsTheCacheSystemThroughTheConstructor()
    {
        $this->assertEquals($this->cache, $this->sdk->getCache());
    }

    /** @test */
    public function testItSetsTheCacheSystemThroughTheSetter()
    {
        $cache = Mockery::mock(CacheInterface::class);
        $this->sdk->setCache($cache);
        $this->assertSame($cache, $this->sdk->getCache());
    }

    /** @test */
    public function testItBuildsARequestObject()
    {
        $this->httpClient->shouldReceive('sendRequest')->andReturnUsing(function (RequestInterface $request) {
            $this->assertEquals('POST', $request->getMethod());
            $this->assertEquals([
                'Host'       => [
                    'api.foobar.com',
                ],
                'some'       => [
                    'header',
                ],
                'some-other' => [
                    'header',
                ],
            ], $request->getHeaders());
            $this->assertEquals('{"foo":"body","bar":"body"}', (string)$request->getBody());
            $this->assertEquals('https://api.foobar.com/foo-bar', (string)$request->getUri());

            return Mockery::mock(ResponseInterface::class, [
                'getStatusCode' => 200,
            ]);
        });

        $this->sdk->doRequest(
            '/foo-bar',
            'post',
            [
                'foo' => 'body',
                'bar' => 'body',
            ],
            [
                'some'       => 'header',
                'some-other' => 'header',
            ]
        );
    }

    /** @test */
    public function testItThrowsARequestExceptionIfTheStatusCodeIsHigherThan300()
    {
        $httpClient = Mockery::mock(HttpClient::class, [
            'sendRequest' => Mockery::mock(ResponseInterface::class, [
                'getStatusCode'   => 401,
                'getReasonPhrase' => 'Authorization required',
            ]),
        ]);
        $this->sdk->setHttpClient($httpClient);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Authorization required');
        $this->expectExceptionCode(401);
        $this->sdk->doRequest('/foo-bar', 'post');
    }

    /** @test */
    public function testItReturnsAResponse()
    {
        $httpClient = Mockery::mock(HttpClient::class, [
            'sendRequest' => Mockery::mock(ResponseInterface::class, [
                'getStatusCode'      => 200,
                'getProtocolVersion' => '1.0',
                'getReasonPhrase'    => '',
                'getHeaders'         => [],
                'getBody'            => '',
            ]),
        ]);
        $this->sdk->setHttpClient($httpClient);
        $response = $this->sdk->doRequest('/foo-bar', 'get');
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}