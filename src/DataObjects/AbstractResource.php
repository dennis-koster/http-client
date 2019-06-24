<?php

namespace Eekhoorn\PhpSdk\DataObjects;

use Eekhoorn\PhpSdk\Contracts\ResourceInterface;
use Eekhoorn\PhpSdk\DataObjects\Relations\HasMany;
use Eekhoorn\PhpSdk\DataObjects\Relations\HasOne;
use Illuminate\Support\Str;
use Jenssegers\Model\Model;

/**
 * @property string $id
 * @property string $type
 */
abstract class AbstractResource extends Model implements ResourceInterface
{
    /**
     * @var array|HasOne[]|HasMany[]
     */
    protected $relationships = [];

    /**
     * @var array
     */
    protected $guarded = ['*'];

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $resourceClass
     * @param string $relationName
     * @return HasOne
     */
    protected function hasOne($resourceClass, $relationName = null): HasOne
    {
        $relationName                         = $relationName ?: Str::snake(debug_backtrace()[1]['function']);
        $relation                             = new HasOne($resourceClass);
        $this->relationships[ $relationName ] = $relation;

        return $relation;
    }

    /**
     * @param string $resourceClass
     * @param string $relationName
     * @return HasMany
     */
    protected function hasMany($resourceClass, $relationName = null): HasMany
    {
        $relationName                         = $relationName ?: Str::snake(debug_backtrace()[1]['function']);
        $relation                             = new HasMany($resourceClass);
        $this->relationships[ $relationName ] = $relation;

        return $relation;
    }

    public function __get($key)
    {
        if (array_key_exists($key, $this->relationships)) {
            return $this->relationships[ $key ]->get();
        }

        return parent::__get($key);
    }
}
