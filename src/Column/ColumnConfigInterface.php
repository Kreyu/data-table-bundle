<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

interface ColumnConfigInterface
{
    public function getName(): string;

    public function getType(): ResolvedColumnTypeInterface;

    public function getOptions(): array;

    public function hasOption(string $name): bool;

    public function getOption(string $name, mixed $default = null): mixed;

    public function getAttributes(): array;

    public function hasAttribute(string $name): bool;

    public function getAttribute(string $name, mixed $default = null): mixed;

    public function getPropertyPath(): ?PropertyPathInterface;

    public function getSortPropertyPath(): ?PropertyPathInterface;

    public function isSortable(): bool;

    public function isExportable(): bool;

    public function isPersonalizable(): bool;
}
