<?php

namespace Eekhoorn\PhpSdk\Contracts;

use Eekhoorn\PhpSdk\DataObjects\ResourceCollection;
use Eekhoorn\PhpSdk\DataObjects\Vacancy;

interface EekhoornApiInterface extends JsonApiSdkInterface, ParsesJsonApiInterface
{
    public const PATH_VACANCIES = '/vacancies';

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @param int   $ttl
     * @return ResourceCollection|Vacancy[]
     */
    public function getVacancies(
        int $page = 1,
        int $pageSize = 100,
        array $filters = [],
        array $includes = [],
        $ttl = self::TTL_10MIN
    ): ResourceCollection;

    /**
     * @param string $id
     * @param array  $includes
     * @param int    $ttl
     * @return Vacancy
     */
    public function getVacancy(string $id, array $includes = [], $ttl = self::TTL_10MIN): Vacancy;
}
