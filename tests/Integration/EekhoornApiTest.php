<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk\Tests\Integration;

use Eekhoorn\PhpSdk\Contracts\EekhoornApiInterface;
use Eekhoorn\PhpSdk\DataObjects\Department;
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

    /** @var JsonApiParser */
    private $parser;

    public function setUp(): void
    {
        parent::setUp();

        $stubsPath       = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'stubs';
        $responseBuilder = new ResponseBuilder($stubsPath);
        $this->client    = new Client($responseBuilder);
        $this->parser    = new JsonApiParser([
            'vacancies'   => Vacancy::class,
            'departments' => Department::class,
        ]);
        $this->sdk       = new EekhoornApi('http://localhost', $this->client, null, $this->parser);
    }

    /** @test */
    public function it_fetches_vacancies()
    {
        $vacancies = $this->sdk->getVacancies();

        dump($vacancies->getLinks());

        $this->assertCount(2, $vacancies);
        /** @var Vacancy $firstVacancy */
        $firstVacancy = $vacancies->get(0);
        $this->assertEquals('vacancy-id-1', $firstVacancy->id);
        $this->assertEquals('Vacancy title 1', $firstVacancy->title);
        $this->assertEquals('Lorem ipsum dolor', $firstVacancy->description);
        $this->assertEquals('vacancies', $firstVacancy->type);

        /** @var Vacancy $secondVacancy */
        $secondVacancy = $vacancies->get(1);
        $this->assertEquals('vacancy-id-2', $secondVacancy->id);
        $this->assertEquals('Vacancy title 2', $secondVacancy->title);
        $this->assertEquals('Lorem ipsum dolor', $secondVacancy->description);
        $this->assertEquals('vacancies', $secondVacancy->type);
    }

    /**
     * @test
     */
    public function it_fetches_a_vacancy()
    {
        $vacancy = $this->sdk->getVacancy('vacancy-id-1');

        $this->assertEquals('vacancy-id-1', $vacancy->id);
        $this->assertEquals('Vacancy title 1', $vacancy->title);
        $this->assertEquals('Lorem ipsum dolor', $vacancy->description);
        $this->assertEquals('vacancies', $vacancy->type);
    }

    /**
     * @test
     */
    public function it_sets_includes_department_on_vacancy()
    {
        try {
            $vacancy = $this->sdk->getVacancy('vacancy-id-1', ['department']);
        } catch (MockNotFoundException $exception) {
            dump($exception->getPossiblePaths());
        }


        dump($vacancy);
    }


}
