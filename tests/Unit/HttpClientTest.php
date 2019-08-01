<?php

declare(strict_types=1);

namespace DennisKoster\HttpClient\Tests\Unit;

use DennisKoster\HttpClient\Exceptions\RequestException;
use DennisKoster\HttpClient\HttpClient;
use Http\Client\HttpClient as HttpPlugHttpClient;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;

class HttpClientTest extends TestCase
{
    /** @var HttpClient */
    private $httpPlugHttpClient;

    /** @var HttpPlugHttpClient */
    private $httpClient;

    /** @var CacheInterface */
    private $cache;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpPlugHttpClient = Mockery::mock(HttpPlugHttpClient::class);
        $this->cache              = Mockery::mock(CacheInterface::class, [
            'get' => null,
            'set' => true,
        ]);
        $this->httpClient         = new HttpClient('https://api.foobar.com', $this->httpPlugHttpClient, $this->cache);
    }

    /** @test */
    public function it_sets_an_http_plug_http_client_through_the_constructor()
    {
        $this->assertSame($this->httpPlugHttpClient, $this->httpClient->getHttpPlugHttpClient());
    }

    /** @test */
    public function it_sets_an_http_plug_http_client_through_the_setter()
    {
        $httpClient = Mockery::mock(HttpPlugHttpClient::class);
        $this->httpClient->setHttpPlugHttpClient($httpClient);
        $this->assertSame($httpClient, $this->httpClient->getHttpPlugHttpClient());
    }

    /** @test */
    public function it_sets_an_api_url_through_the_constructor()
    {
        $this->assertSame('https://api.foobar.com', $this->httpClient->getApiUrl());
    }

    /** @test */
    public function it_sets_an_api_url_through_the_setter()
    {
        $this->httpClient->setApiUrl('http://api.foo.bar');
        $this->assertEquals('http://api.foo.bar', $this->httpClient->getApiUrl());
    }

    /** @test */
    public function it_sets_the_cache_system_through_the_constructor()
    {
        $this->assertSame($this->cache, $this->httpClient->getCache());
    }

    /** @test */
    public function it_sets_the_cache_system_through_the_setter()
    {
        $cache = Mockery::mock(CacheInterface::class);
        $this->httpClient->setCache($cache);
        $this->assertSame($cache, $this->httpClient->getCache());
    }

    /** @test */
    public function it_builds_a_request_object()
    {
        $this->httpPlugHttpClient->shouldReceive('sendRequest')->andReturnUsing(function (RequestInterface $request) {
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

        $this->httpClient->doRequest(
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
    public function it_throws_a_request_exception_if_the_status_code_is_higher_than_300()
    {
        $httpClient = Mockery::mock(HttpPlugHttpClient::class, [
            'sendRequest' => Mockery::mock(ResponseInterface::class, [
                'getStatusCode'   => 401,
                'getReasonPhrase' => 'Authorization required',
            ]),
        ]);
        $this->httpClient->setHttpPlugHttpClient($httpClient);

        $this->expectException(RequestException::class);
        $this->expectExceptionMessage('Authorization required');
        $this->expectExceptionCode(401);
        $this->httpClient->doRequest('/foo-bar', 'post');
    }

    /** @test */
    public function it_returns_a_response()
    {
        $httpClient = Mockery::mock(HttpPlugHttpClient::class, [
            'sendRequest' => Mockery::mock(ResponseInterface::class, [
                'getStatusCode'      => 200,
                'getProtocolVersion' => '1.0',
                'getReasonPhrase'    => '',
                'getHeaders'         => [],
                'getBody'            => '',
            ]),
        ]);
        $this->httpClient->setHttpPlugHttpClient($httpClient);
        $response = $this->httpClient->doRequest('/foo-bar', 'get');
        $this->assertInstanceOf(ResponseInterface::class, $response);
    }
}
