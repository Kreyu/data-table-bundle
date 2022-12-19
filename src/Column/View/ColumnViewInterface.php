<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\View;

interface ColumnViewInterface
{
    public function getVariables(): array;

    public function getVariable(string $name): mixed;

    public function hasVariable(string $name): bool;

    public function setVariable(string $name, mixed $value): void;
}
