<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk\Tests\Integration;

use Eekhoorn\PhpSdk\Contracts\EekhoornApiInterface;
use Eekhoorn\PhpSdk\DataObjects\Vacancy;
use Eekhoorn\PhpSdk\EekhoornApi;
use Eekhoorn\PhpSdk\JsonApiParser;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Swis\Http\Fixture\Client;
use Swis\Http\Fixture\MockNotFoundException;
use Swis\Http\Fixture\ResponseBuilder;

class EekhoornApiTest extends TestCase
{
    /** @var ClientInterface */
    private $client;

    /** @var EekhoornApiInterface */
    private $sdk;

    public function setUp(): void
    {
        parent::setUp();

        $stubsPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'stubs';
        $responseBuilder = new ResponseBuilder($stubsPath);
        $this->client = new Client($responseBuilder);
        $this->sdk = new EekhoornApi('http://localhost', $this->client);
    }

    /** @test */
    public function testItFetchesVacancies()
    {
        $response = $this->sdk->getVacancies(
            1,
            100,
            ['title' => 'henk'],
            ['department'],
            0
        );
        $body = $response->getContents();

        $parser = new JsonApiParser([
            'vacancies' => Vacancy::class
        ]);
        var_dump($parser->parse($body));
    }


}
