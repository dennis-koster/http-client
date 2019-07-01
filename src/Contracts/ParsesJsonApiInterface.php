<?php

namespace Eekhoorn\PhpSdk\Contracts;

use Eekhoorn\PhpSdk\JsonApiParser;

interface ParsesJsonApiInterface
{
    /**
     * @param JsonApiParser $parser
     * @return ParsesJsonApiInterface
     */
    public function setParser(?JsonApiParser $parser): ParsesJsonApiInterface;

    /**
     * @return JsonApiParser|null
     */
    public function getParser(): ?JsonApiParser;
}
