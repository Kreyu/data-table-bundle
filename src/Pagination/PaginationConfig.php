<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

class PaginationConfig
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