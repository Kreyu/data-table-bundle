<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Extension;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;

interface ActionExtensionInterface
{
    public function getType(string $name): ActionTypeInterface;

    public function hasType(string $name): bool;

    public function getTypeExtensions(string $name): array;

    public function hasTypeExtensions(string $name): bool;
}
