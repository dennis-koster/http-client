<?php

declare(strict_types=1);

namespace Eekhoorn\PhpSdk;

use Eekhoorn\PhpSdk\Contracts\EekhoornApiInterface;
use Eekhoorn\PhpSdk\DataObjects\ResourceCollection;
use Eekhoorn\PhpSdk\DataObjects\Vacancy;
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
     * @return EekhoornApi
     */
    public function setParser(?JsonApiParser $parser): EekhoornApiInterface
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
        $ttl = self::TTL_10MIN
    ): ResourceCollection {
        $parameters = $this->buildGetParameters($page, $pageSize, $filters, $includes);

        $response = $this->doRequest(self::PATH_VACANCIES, self::METHOD_GET, $parameters, [], $ttl);
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
    public function getVacancy(string $id, array $includes = [], $ttl = self::TTL_10MIN): Vacancy
    {
        $response = $this->doRequest(
            self::PATH_VACANCIES . '/' . $id,
            self::METHOD_GET,
            ['includes' => $includes],
            [],
            $ttl
        );

        $body     = $response->getBody();
        $content  = $body->getContents();

        return $this->parser->parse($content);
    }
}
