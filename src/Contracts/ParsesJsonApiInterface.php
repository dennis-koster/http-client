<?php

namespace Eekhoorn\PhpSdk\Contracts;

use Eekhoorn\PhpSdk\EekhoornApi;
use Eekhoorn\PhpSdk\JsonApiParser;

interface ParsesJsonApiInterface
{
    /**
     * @param JsonApiParser $parser
     * @return EekhoornApi
     */
    public function setParser(?JsonApiParser $parser): EekhoornApiInterface;

    /**
     * @return JsonApiParser|null
     */
    public function getParser(): ?JsonApiParser;
}
