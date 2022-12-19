<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

class Column implements ColumnInterface
{
    public function __construct(
        private readonly string $name,
        private readonly ColumnTypeInterface $type,
        private readonly array $options = [],
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ColumnTypeInterface
    {
        return $this->type;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
