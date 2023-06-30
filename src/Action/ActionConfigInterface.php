<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;

interface ActionConfigInterface
{
    public function getName(): string;

    public function getType(): ResolvedActionTypeInterface;

    public function getOptions(): array;

    public function hasOption(string $name): bool;

    public function getOption(string $name, mixed $default = null): mixed;

    public function getAttributes(): array;

    public function hasAttribute(string $name): bool;

    public function getAttribute(string $name, mixed $default = null): mixed;

    public function isBatch(): bool;

    public function isConfirmable(): bool;
}
