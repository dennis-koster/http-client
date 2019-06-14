<?php

namespace Eekhoorn\PhpSdk\Contracts;

use Eekhoorn\PhpSdk\JsonApiParser;
use Psr\Http\Message\StreamInterface;
use Tightenco\Collect\Support\Collection;

interface EekhoornApiInterface extends JsonApiSdkInterface
{
    public const PATH_VACANCIES = '/vacancies';

    /**
     * @param JsonApiParser $parser
     * @return self
     */
    public function setParser(JsonApiParser $parser): self;

    /**
     * @return JsonApiParser|null
     */
    public function getParser(): ?JsonApiParser;

    /**
     * @param int   $page
     * @param int   $pageSize
     * @param array $filters
     * @param array $includes
     * @param int   $ttl
     * @return Collection
     */
    public function getVacancies(
        int $page = 1,
        int $pageSize = 100,
        array $filters = [],
        array $includes = [],
        $ttl = self::TTL_10MIN
    ): Collection;
}
