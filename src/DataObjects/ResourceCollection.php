<?php

namespace Eekhoorn\PhpSdk\DataObjects;

use Illuminate\Support\Collection;

class ResourceCollection extends Collection
{
    /**
     * @var array
     */
    protected $links = [];

    /**
     * @var array
     */
    protected $included = [];

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     * @return $this
     */
    public function setLinks(array $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return array
     */
    public function getIncluded(): array
    {
        return $this->included;
    }

    /**
     * @param array $included
     * @return $this
     */
    public function setIncluded(array $included): self
    {
        $this->included = $included;

        return $this;
    }
}
