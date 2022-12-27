<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Mapper;

use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

interface FilterMapperInterface
{
    /**
     * @param class-string<FilterInterface> $typeClass
     */
    public function add(string $name, string $typeClass, array $options = []): static;

    public function get(string $name): ?FilterInterface;

    public function has(string $name): bool;

    public function remove(string $name): self;

    /**
     * @return array<FilterInterface>
     */
    public function all(): array;
}
