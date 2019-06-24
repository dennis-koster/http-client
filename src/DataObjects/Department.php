<?php

namespace Eekhoorn\PhpSdk\DataObjects;

/**
 * @property string $name
 * @property string $description
 */
class Department extends AbstractResource
{
    protected $fillable = [
        'name',
        'description',
    ];
}
