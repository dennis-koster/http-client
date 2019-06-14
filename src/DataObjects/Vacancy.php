<?php

namespace Eekhoorn\PhpSdk\DataObjects;

/**
 * @property string          $title
 * @property string          $description
 * @property null|Department $department
 */
class Vacancy extends AbstractDataObject
{
    protected $fillable = [
        'title',
        'description'
    ];

}
