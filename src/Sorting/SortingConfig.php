<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

class SortingConfig
{
    public function __construct(
        private bool $enabled,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
