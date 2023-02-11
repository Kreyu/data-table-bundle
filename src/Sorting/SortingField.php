<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

class SortingField
{
    public function __construct(
        private string $name,
        private string $direction = 'asc',
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDirection(): string
    {
        return $this->direction;
    }
}
