<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

class FilterBuilder extends FilterConfigBuilder implements FilterBuilderInterface
{
    public function getFilter(): FilterInterface
    {
        $this->ensureNotLocked();

        return new Filter($this->getFilterConfig());
    }
}
