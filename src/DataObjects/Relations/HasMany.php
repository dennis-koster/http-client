<?php

namespace Eekhoorn\PhpSdk\DataObjects\Relations;

use Eekhoorn\PhpSdk\Contracts\ResourceInterface;
use Eekhoorn\PhpSdk\DataObjects\ResourceCollection;

class HasMany
{
    /**
     * @var string
     */
    protected $resourceType;

    /**
     * @var ResourceCollection|null
     */
    protected $resources;

    /**
     * @var string[]
     */
    protected $ids = [];

    /**
     * @param string $resourceType
     */
    public function __construct(string $resourceType)
    {
        $this->resourceType = $resourceType;
    }

    /**
     * @param string[] $identifiers
     * @return self
     */
    public function setIds(array $identifiers): self
    {
        $this->ids = $identifiers;

        return $this;
    }

    public function addId(string $id): self
    {
        $this->ids[] = $id;

        return $this;
    }

    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * @param array|ResourceInterface $resource
     * @return self
     */
    public function associate($resource): self
    {
        if ($this->resources === null) {
            $this->resources = new ResourceCollection();
        }

        if ($resource instanceof ResourceInterface) {
            $this->resources->push($resource);
            $this->addId($resource->getId());

            return $this;
        }

        if (is_array($resource)) {
            foreach ($resource as $singleResource) {
                $this->associate($singleResource);
            }
        }

        return $this;
    }

    /**
     * @return ResourceCollection|null
     */
    public function get(): ?ResourceCollection
    {
        return $this->resources;
    }
}
