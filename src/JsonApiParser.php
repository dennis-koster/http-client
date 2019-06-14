<?php

namespace Eekhoorn\PhpSdk;

use Jenssegers\Model\Model;
use Tightenco\Collect\Support\Collection;

class JsonApiParser
{
    /**
     * @var array
     */
    protected $typeMapping;

    /**
     * @param array $typeMapping
     */
    public function __construct(array $typeMapping = [])
    {
        $this->typeMapping = $typeMapping;
    }

    /**
     * @param string $jsonApiData
     * @return Collection|Model[]|Model
     */
    public function parse(string $jsonApiData)
    {
        $decoded = json_decode($jsonApiData, true);
        if ( ! array_key_exists('data', $decoded)) {
            throw new \RuntimeException('Could not parse given json data');

        }

        $data = $decoded['data'];

        if ( ! $this->isCollection($data)) {
            return $this->parseSingleItem($data);
        }

        return $this->parseItemCollection($data);
    }

    /**
     * Builds a collection of models from data sets
     *
     * @param array $dataSets
     * @return Collection
     */
    public function parseItemCollection(array $dataSets)
    {
        $modelClass = null;
        $collection = new Collection();

        foreach ($dataSets as $dataSet) {
            if ($modelClass === null) {
                $modelClass = $this->determineModelClass($dataSet['type']);
            }

            $collection->push($this->parseSingleItem($dataSet));
        }

        return $collection;
    }

    /**
     * Builds a single model from given data set
     *
     * @param array $data
     * @return Model
     */
    public function parseSingleItem(array $data)
    {
        $modelClass = $this->determineModelClass($data['type']);

        /** @var Model $model */
        $model = new $modelClass();
        $model->forceFill([
            'id' => $data['id']
        ]);
        $model->fill($data['attributes']);

        return $model;
    }

    /**
     * Determines FQN of model to make for given resource type
     *
     * @param string $resourceType
     * @return string
     */
    protected function determineModelClass(string $resourceType): string
    {
        if (!array_key_exists($resourceType, $this->typeMapping)) {
            throw new \RuntimeException('Could not determine model for given type "' . $resourceType . '".');
        }

        return $this->typeMapping[$resourceType];
    }

    /**
     * Determines whether given data is a collection of resource
     * or just a single resource.
     *
     * @param array $data
     * @return bool
     */
    protected function isCollection(array $data): bool
    {
        foreach ($data as $key => $value) {
            if ( ! is_int($key)) {
                return false;
            }
        }

        return true;
    }
}
