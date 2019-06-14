<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk\Tests\Unit;

use Eekhoorn\PhpSdk\EekhoornApi;
use Eekhoorn\PhpSdk\Exceptions\RequestException;
use Eekhoorn\PhpSdk\JsonApiParser;
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

    /** @var JsonApiParser|Mockery\MockInterface */
    private $parser;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = Mockery::mock(HttpClient::class);
        $this->parser = Mockery::mock(JsonApiParser::class);
        $this->cache = Mockery::mock(CacheInterface::class, [
            'get' => null,
            'set' => true,
        ]);
        $this->sdk = new EekhoornApi('https://api.foobar.com', $this->httpClient, $this->cache, $this->parser);
    }

    /** @test */
    public function it_sets_an_http_client_through_the_constructor()
    {
        $this->assertSame($this->httpClient, $this->sdk->getHttpClient());
    }

    /** @test */
    public function it_sets_an_http_client_through_the_setter()
    {
        $httpClient = Mockery::mock(HttpClient::class);
        $this->sdk->setHttpClient($httpClient);
        $this->assertSame($httpClient, $this->sdk->getHttpClient());
    }

    /** @test */
    public function it_sets_an_api_url_through_the_constructor()
    {
        $this->assertSame('https://api.foobar.com', $this->sdk->getApiUrl());
    }

    /** @test */
    public function it_sets_an_api_url_through_the_setter()
    {
        $this->sdk->setApiUrl('http://api.foo.bar');
        $this->assertEquals('http://api.foo.bar', $this->sdk->getApiUrl());
    }

    /** @test */
    public function it_sets_the_cache_system_through_the_constructor()
    {
        $this->assertSame($this->cache, $this->sdk->getCache());
    }

    /** @test */
    public function it_sets_the_cache_system_through_the_setter()
    {
        $cache = Mockery::mock(CacheInterface::class);
        $this->sdk->setCache($cache);
        $this->assertSame($cache, $this->sdk->getCache());
    }

    /**
     * @test
     */
    public function it_sets_the_parser_through_the_constructor()
    {
        $this->assertSame($this->parser, $this->sdk->getParser());
    }

    /**
     * @test
     */
    public function it_sets_the_parser_through_the_setter()
    {
        $parser = Mockery::mock(JsonApiParser::class);
        $this->sdk->setParser($parser);
        $this->assertSame($parser, $this->sdk->getParser());
    }

    /** @test */
    public function it_builds_a_request_object()
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
    public function it_throws_a_request_exception_if_the_status_code_is_higher_than_300()
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
    public function it_returns_a_response()
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
