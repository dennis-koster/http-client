<?php

namespace Eekhoorn\PhpSdk\DataObjects\Relations;

use Eekhoorn\PhpSdk\Contracts\ResourceInterface;

class HasOne
{
    /**
     * @var string
     */
    protected $resourceType;

    /**
     * @var ResourceInterface|null
     */
    protected $included ;

    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $resourceType
     */
    public function __construct(string $resourceType)
    {
        $this->resourceType = $resourceType;
    }

    /**
     * @param string $identifier
     * @return self
     */
    public function setId(string $identifier): self
    {
        $this->id = $identifier;

        return $this;
    }

    /**
     * @param ResourceInterface $resource
     * @return self
     */
    public function associate(ResourceInterface $resource): self
    {
        $this->setId($resource->getId());

        $this->included = $resource;

        return $this;
    }

    /**
     * @return ResourceInterface|null
     */
    public function get(): ?ResourceInterface
    {
        return $this->included;
    }
}
