<?php

namespace Eekhoorn\PhpSdk\DataObjects;

class Department extends AbstractResource
{
    protected $fillable = [
        'name',
        'description',
    ];
}
