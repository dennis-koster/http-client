<?php

namespace Eekhoorn\PhpSdk\Contracts;

use Psr\Http\Message\StreamInterface;

interface EekhoornApiInterface extends JsonApiSdkInterface
{
    public const PATH_VACANCIES = '/vacancies';

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @param int   $ttl
     * @return StreamInterface
     */
    public function getVacancies(
        int $page = 1,
        int $pageSize = 100,
        array $filters = [],
        array $includes = [],
        $ttl = self::TTL_10MIN
    ): StreamInterface;
}
