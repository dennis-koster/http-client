<?php

namespace Eekhoorn\PhpSdk;

use Eekhoorn\PhpSdk\Contracts\ResourceInterface;
use Eekhoorn\PhpSdk\DataObjects\AbstractResource;
use Eekhoorn\PhpSdk\DataObjects\ResourceCollection;
use Illuminate\Support\Str;
use Psr\Http\Message\ResponseInterface;

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
     * @return ResourceCollection|ResourceInterface
     */
    public function parse(string $jsonApiData)
    {
        $decoded = json_decode($jsonApiData, true);
        if ( ! array_key_exists('data', $decoded)) {
            throw new \RuntimeException('Could not parse given json data');
        }

        $data     = $decoded['data'];
        $links    = array_key_exists('links', $decoded) ? $decoded['links'] : [];
        $included = array_key_exists('included', $decoded) ? $decoded['included'] : [];

        if ( ! $this->isCollection($data)) {
            return $this->parseSingleItem($data, $included);
        }

        return $this->parseItemCollection($data, $links, $included);
    }

    /**
     * Builds a collection of models from data sets
     *
     * @param array $dataSets
     * @param array $links
     * @param array $included
     * @return ResourceCollection
     */
    public function parseItemCollection(array $dataSets, array $links, array $included)
    {
        $modelClass = null;
        $collection = new ResourceCollection();

        foreach ($dataSets as $dataSet) {
            if ($modelClass === null) {
                $modelClass = $this->determineModelClass($dataSet['type']);
            }

            $collection->push($this->parseSingleItem($dataSet));
        }

        $collection
            ->setLinks($links)
            ->setIncluded($included);

        return $collection;
    }

    /**
     * Builds a single model from given data set
     *
     * @param array $data
     * @param array $included
     * @return ResourceInterface
     */
    public function parseSingleItem(array $data, array $included = [])
    {
        $modelClass = $this->determineModelClass($data['type']);

        /** @var AbstractResource $model */
        $model = new $modelClass();
        $model->forceFill([
            'id'   => $data['id'],
            'type' => $data['type'],
        ]);

        if (array_key_exists('attributes', $data)) {
            $model->fill($data['attributes']);
        }

        if (array_key_exists('relationships', $data)) {
            $model = $this->setRelations($model, $data['relationships'], $included);
        }

        return $model;
    }

    protected function setRelations(ResourceInterface $resource, array $relations = [], array $included = []): ResourceInterface
    {
        foreach ($relations as $relationName => $relationData) {
            $relationIdentifiers = $relationData['data'];
            $relationName        = Str::camel($relationName);

            if ( ! method_exists($resource, $relationName)) {
                continue;
            }

            $resource->$relationName()->setId($relationIdentifiers['id']);

            if ( ! empty($included)) {
                foreach ($included as $include) {

                    if (    $include['type'] === $relationIdentifiers['type']
                        &&  $include['id'] === $relationIdentifiers['id']
                    ) {
                        $relatedResource = $this->parseSingleItem($include);
                        $resource->$relationName()->associate($relatedResource);
                    }

                    continue 2;
                }
            }
        }

        return $resource;
    }

    /**
     * Determines FQN of model to make for given resource type
     *
     * @param string $resourceType
     * @return string
     */
    protected function determineModelClass(string $resourceType): string
    {
        if ( ! array_key_exists($resourceType, $this->typeMapping)) {
            throw new \RuntimeException('Could not determine model for given type "' . $resourceType . '".');
        }

        return $this->typeMapping[ $resourceType ];
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
