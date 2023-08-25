<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;

interface FilterConfigInterface
{
    public function getName(): string;

    public function getType(): ResolvedFilterTypeInterface;

    public function getOptions(): array;

    public function hasOption(string $name): bool;

    public function getOption(string $name, mixed $default = null): mixed;

    /**
     * @return array<Operator>
     */
    public function getSupportedOperators(): array;

    public function getDefaultOperator(): Operator;

    public function isOperatorSelectable(): bool;
}
