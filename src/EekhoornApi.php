<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk;

use Eekhoorn\PhpSdk\Contracts\EekhoornApiInterface;
use Eekhoorn\PhpSdk\Contracts\ParsesJsonApiInterface;
use Eekhoorn\PhpSdkInterface\DataObjects\Department;
use Eekhoorn\PhpSdkInterface\DataObjects\Employee;
use Eekhoorn\PhpSdkInterface\DataObjects\Location;
use Eekhoorn\PhpSdkInterface\DataObjects\ResourceCollection;
use Eekhoorn\PhpSdkInterface\DataObjects\Vacancy;
use Eekhoorn\PhpSdkInterface\DataObjects\VacancyNotice;
use Eekhoorn\PhpSdkInterface\Enums\CacheDurationsEnum;
use Eekhoorn\PhpSdkInterface\Enums\HttpMethodsEnum;
use Eekhoorn\PhpSdk\Exceptions\RequestException;
use Http\Client\HttpClient;
use Psr\SimpleCache\CacheInterface;

class EekhoornApi extends JsonApiSdk implements EekhoornApiInterface
{

    /** @var JsonApiParser|null */
    protected $parser;

    /**
     * @param string              $apiUrl
     * @param HttpClient|null     $httpClient
     * @param CacheInterface|null $cache
     * @param JsonApiParser       $parser
     */
    public function __construct(
        string $apiUrl,
        HttpClient $httpClient = null,
        CacheInterface $cache = null,
        JsonApiParser $parser = null
    ) {
        parent::__construct($apiUrl, $httpClient, $cache);

        $this->setParser($parser);
    }

    /**
     * @param JsonApiParser $parser
     * @return ParsesJsonApiInterface
     */
    public function setParser(?JsonApiParser $parser): ParsesJsonApiInterface
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * @return JsonApiParser|null
     */
    public function getParser(): ?JsonApiParser
    {
        return $this->parser;
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @param int   $ttl
     * @return ResourceCollection
     * @throws RequestException
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getVacancies(
        int $page = 1,
        int $pageSize = 100,
        array $filters = [],
        array $includes = [],
        $ttl = CacheDurationsEnum::DURATION_10_MIN
    ): ResourceCollection {
        $parameters = $this->buildGetParameters($page, $pageSize, $filters, $includes);

        $response = $this->doRequest(self::PATH_VACANCIES, HttpMethodsEnum::GET, $parameters, [], $ttl);
        $body     = $response->getBody();
        $content  = $body->getContents();

        return $this->parser->parse($content);
    }

    /**
     * @param string $id
     * @param array  $includes
     * @param int    $ttl
     * @return Vacancy
     * @throws RequestException
     * @throws \Http\Client\Exception
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getVacancy(string $id, array $includes = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): Vacancy
    {
        $response = $this->doRequest(
            self::PATH_VACANCIES . '/' . $id,
            HttpMethodsEnum::GET,
            ['includes' => $includes],
            [],
            $ttl
        );

        $body     = $response->getBody();
        $content  = $body->getContents();

        return $this->parser->parse($content);
    }

    public function setLanguage(string $language = self::DEFAULT_LANGUAGE): \Eekhoorn\PhpSdkInterface\Contracts\EekhoornApiInterface
    {
        // TODO: Implement setLanguage() method.
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @param int   $ttl
     * @return ResourceCollection|Department[]
     */
    public function getDepartments(int $page = 1, int $pageSize = 100, array $filters = [], array $includes = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): ResourceCollection
    {
        // TODO: Implement getDepartments() method.
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @param int   $ttl
     * @return ResourceCollection|Employee[]
     */
    public function getEmployees(int $page = 1, int $pageSize = 100, array $filters = [], array $includes = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): ResourceCollection
    {
        // TODO: Implement getEmployees() method.
    }

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @param int   $ttl
     * @return ResourceCollection|Location[]
     */
    public function getLocations(int $page = 1, int $pageSize = 100, array $filters = [], array $includes = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): ResourceCollection
    {
        // TODO: Implement getLocations() method.
    }

    /**
     * @param string $id
     * @param array  $includes
     * @param int    $ttl
     * @return Location
     */
    public function getLocation(string $id, array $includes = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): Location
    {
        // TODO: Implement getLocation() method.
    }

    /**
     * @param string $id
     * @param array  $includes
     * @param int    $ttl
     * @return Employee
     */
    public function getEmployee(string $id, array $includes = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): Employee
    {
        // TODO: Implement getEmployee() method.
    }

    /**
     * @param string $id
     * @param array  $includes
     * @param int    $ttl
     * @return Department
     */
    public function getDepartment(string $id, array $includes = [], $ttl = CacheDurationsEnum::DURATION_10_MIN): Department
    {
        // TODO: Implement getDepartment() method.
    }

    /**
     * @param VacancyNotice $vacancyNotice
     * @param string        $language
     * @return VacancyNotice
     */
    public function createVacancyNotice(VacancyNotice $vacancyNotice, string $language = self::DEFAULT_LANGUAGE): VacancyNotice
    {
        // TODO: Implement createVacancyNotice() method.
    }
}
