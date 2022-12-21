<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\View;

class ColumnView implements ColumnViewInterface
{
    public function __construct(
        private array $variables = [],
    ) {
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function getVariable(string $name, mixed $default = null): mixed
    {
        return $this->variables[$name] ?? $default;
    }

    public function hasVariable(string $name): bool
    {
        return array_key_exists($name, $this->variables);
    }

    public function setVariable(string $name, mixed $value): void
    {
        $this->variables[$name] = $value;
    }
}
