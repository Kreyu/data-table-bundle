<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Mapper;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;

interface ColumnMapperInterface
{
    /**
     * @param class-string<ColumnTypeInterface> $typeClass
     */
    public function add(string $name, string $typeClass, array $options = []): static;

    public function get(string $name): ?ColumnInterface;

    public function has(string $name): bool;

    public function remove(string $name): self;

    /**
     * @return array<ColumnInterface>
     */
    public function all(): array;
}
