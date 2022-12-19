<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Mapper;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

interface ColumnMapperInterface
{
    public function add(string $name, ?string $type = null, array $options = []): static;

    public function get(string $name): ?ColumnInterface;

    public function has(string $name): bool;

    public function remove(string $name): self;

    /**
     * @return array<ColumnInterface>
     */
    public function all(): array;
}
