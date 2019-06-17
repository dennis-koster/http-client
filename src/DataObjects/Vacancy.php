<?php

namespace Eekhoorn\PhpSdk\DataObjects;

use Eekhoorn\PhpSdk\DataObjects\Relations\HasOne;

/**
 * @property string          $title
 * @property string          $description
 * @property null|Department $department
 */
class Vacancy extends AbstractResource
{
    protected $fillable = [
        'title',
        'description'
    ];

    public function department(): HasOne
    {
        return $this->hasOne(Department::class);
    }

    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

}
