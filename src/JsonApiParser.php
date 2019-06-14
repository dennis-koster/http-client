<?php

namespace Eekhoorn\PhpSdk;

use Eekhoorn\PhpSdk\DataObjects\Vacancy;
use Jenssegers\Model\Model;

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
     * @return Model[]|Model
     */
    public function parse(string $jsonApiData)
    {
        $decoded = json_decode($jsonApiData, true);
        if ( ! array_key_exists('data', $decoded)) {
            throw new \RuntimeException('Could not parse given json data');

        }

        $data = $decoded['data'];

        if ( ! $this->isCollection($data)) {
            return $this->parseSingle($data);
        }

        return $this->parseCollection($data);
    }

    public function parseCollection(array $dataSets)
    {
        $modelClass = null;
        $collection = [];

        foreach ($dataSets as $dataSet) {
            if ($modelClass === null) {
                $modelClass = $this->determineModel($dataSet['type']);
            }

            $collection[] = $this->parseSingle($dataSet);
        }

        return $collection;
    }

    /**
     * @param array $data
     * @return Model
     */
    public function parseSingle(array $data)
    {
        $modelClass = $this->determineModel($data['type']);

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
    protected function determineModel(string $resourceType): string
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
