<?php

namespace Eekhoorn\PhpSdk\Contracts;

interface ResourceInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getId(): string;
}
