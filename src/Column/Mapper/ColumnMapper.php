<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Mapper;

use Kreyu\Bundle\DataTableBundle\Column\Factory\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

class ColumnMapper implements ColumnMapperInterface
{
    private array $columns = [];

    public function __construct(
        private readonly ColumnFactoryInterface $columnFactory,
    ) {
    }

    public function add(string $name, ?string $type = null, array $options = []): static
    {
        $this->columns[$name] = $this->columnFactory->create($name, $type, $options);

        return $this;
    }

    public function get(string $name): ?ColumnInterface
    {
        return $this->columns[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    public function remove(string $name): ColumnMapperInterface
    {
        unset($this->columns[$name]);

        return $this;
    }

    public function all(): array
    {
        return $this->columns;
    }
}
